document.addEventListener('alpine:init', () => {
    Alpine.data('knowledgeMapBuilder', (config) => ({
        nodes: config.nodes || [],
        connections: config.connections || [],
        zoom: config.zoom || 1,
        canvasWidth: config.canvasWidth || 4000,
        canvasHeight: config.canvasHeight || 3000,
        offsetX: 0,
        offsetY: 0,

        leftPanelOpen: true,
        rightPanelOpen: false,
        focusMode: false,

        isPanning: false,
        isDraggingNode: false,
        draggedNode: null,

        isConnectionMode: false,
        isDrawingConnection: false,
        drawingFromNodeId: null,
        drawStartX: 0,
        drawStartY: 0,
        drawCurrentX: 0,
        drawCurrentY: 0,

        selectedNodeId: null,
        selectedConnectionId: null,
        lastMouseX: 0,
        lastMouseY: 0,
        justSelectedConnection: false,

        init() {
            this.$nextTick(() => {
                this.fitView();
                this.renderPermanentConnections();
            });

            window.addEventListener('km-node-added', (e) => {
                const node = e.detail?.node || e.detail?.[0]?.node;

                if (!node) return;

                this.nodes.push({
                    ...node,
                    position_x: parseFloat(node.position_x),
                    position_y: parseFloat(node.position_y),
                });

                this.selectNode(node.id, true);
            });

            window.addEventListener('km-refresh', (e) => {
                const data = e.detail?.[0] || e.detail;

                if (!data || !Array.isArray(data.nodes)) return;

                this.nodes = data.nodes.map((node) => ({
                    ...node,
                    position_x: parseFloat(node.position_x),
                    position_y: parseFloat(node.position_y),
                }));

                this.connections = (data.connections || []).map((connection) => ({ ...connection }));

                this.isDrawingConnection = false;
                this.drawingFromNodeId = null;

                this.$nextTick(() => this.renderPermanentConnections());
            });

            window.addEventListener('km-toggle-focus', () => this.toggleFocus());

            window.addEventListener('keydown', (e) => {
                const activeTag = document.activeElement?.tagName;

                if (
                    e.code === 'Space'
                    && activeTag !== 'INPUT'
                    && activeTag !== 'TEXTAREA'
                    && activeTag !== 'SELECT'
                ) {
                    e.preventDefault();

                    if (this.$refs.shell) {
                        this.$refs.shell.style.cursor = 'grab';
                    }
                }

                if (e.code === 'Escape') {
                    if (this.isDrawingConnection) {
                        this.cancelDrawingConnection();
                        return;
                    }

                    if (this.isConnectionMode) {
                        this.isConnectionMode = false;
                        return;
                    }

                    if (this.rightPanelOpen) {
                        this.closeInspector();
                    }
                }
            });

            window.addEventListener('keyup', (e) => {
                if (e.code === 'Space' && this.$refs.shell) {
                    this.$refs.shell.style.cursor = this.isConnectionMode ? 'crosshair' : 'default';
                }
            });

            window.addEventListener('resize', () => {
                window.requestAnimationFrame(() => this.renderPermanentConnections());
            });
        },

        centerCanvas() {
            const shell = this.$refs.shell;

            if (!shell) return;

            this.offsetX = (shell.offsetWidth / 2) - ((this.canvasWidth / 2) * this.zoom);
            this.offsetY = (shell.offsetHeight / 2) - ((this.canvasHeight / 2) * this.zoom);
        },

        zoomIn() {
            const oldZoom = this.zoom;
            this.zoom = Math.min(this.zoom + 0.1, 2.5);
            this.adjustOffsetForZoom(oldZoom);
            this.$nextTick(() => this.renderPermanentConnections());
        },

        zoomOut() {
            const oldZoom = this.zoom;
            this.zoom = Math.max(this.zoom - 0.1, 0.3);
            this.adjustOffsetForZoom(oldZoom);
            this.$nextTick(() => this.renderPermanentConnections());
        },

        resetZoom() {
            this.zoom = 1;
            this.centerCanvas();
            this.$nextTick(() => this.renderPermanentConnections());
        },

        fitView() {
            if (!this.nodes.length) {
                this.resetZoom();
                return;
            }

            let minX = Infinity;
            let minY = Infinity;
            let maxX = -Infinity;
            let maxY = -Infinity;

            this.nodes.forEach((node) => {
                const x = parseFloat(node.position_x) || 0;
                const y = parseFloat(node.position_y) || 0;

                minX = Math.min(minX, x);
                minY = Math.min(minY, y);
                maxX = Math.max(maxX, x + 180);
                maxY = Math.max(maxY, y + 90);
            });

            const shell = this.$refs.shell;

            if (!shell) return;

            const padding = 140;
            const width = (maxX - minX) + (padding * 2);
            const height = (maxY - minY) + (padding * 2);

            this.zoom = Math.max(
                0.3,
                Math.min(shell.offsetWidth / width, shell.offsetHeight / height, 1)
            );

            this.offsetX = (shell.offsetWidth / 2) - ((minX + ((maxX - minX) / 2)) * this.zoom);
            this.offsetY = (shell.offsetHeight / 2) - ((minY + ((maxY - minY) / 2)) * this.zoom);

            this.$nextTick(() => this.renderPermanentConnections());
        },

        adjustOffsetForZoom(oldZoom) {
            const shell = this.$refs.shell;

            if (!shell || oldZoom <= 0) return;

            const centerX = shell.offsetWidth / 2;
            const centerY = shell.offsetHeight / 2;

            this.offsetX = centerX - ((centerX - this.offsetX) * (this.zoom / oldZoom));
            this.offsetY = centerY - ((centerY - this.offsetY) * (this.zoom / oldZoom));
        },

        getCanvasCoords(clientX, clientY) {
            const canvasRect = this.$refs.canvas.getBoundingClientRect();

            return {
                x: (clientX - canvasRect.left) / this.zoom,
                y: (clientY - canvasRect.top) / this.zoom,
            };
        },

        handleCanvasPointerDown(e) {
            if (
                e.button !== 0
                || e.target.closest('.km-node')
                || e.target.closest('.km-connection-group')
                || e.target.closest('.km-quick-actions')
                || e.target.closest('.km-floating-toolbar')
                || e.target.closest('.km-canvas-controls')
            ) {
                return;
            }

            this.startPan(e);
        },

        handleCanvasClick(e) {
            if (
                this.justSelectedConnection
                || e.target.closest('.km-connection-group')
                || e.target.closest('.km-quick-actions')
                || e.target.closest('.km-node')
                || e.target.closest('.km-floating-toolbar')
                || e.target.closest('.km-canvas-controls')
            ) {
                return;
            }

            if (this.isConnectionMode) {
                this.cancelDrawingConnection();
                return;
            }

            this.deselect();
        },

        handleSidebarDragStart(e, nodeId) {
            e.dataTransfer.setData('text/plain', nodeId);
            e.dataTransfer.effectAllowed = 'move';
        },

        handleCanvasDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        },

        handleCanvasDrop(e) {
            e.preventDefault();

            const nodeId = e.dataTransfer.getData('text/plain');

            if (!nodeId) return;

            const coords = this.getCanvasCoords(e.clientX, e.clientY);
            const node = this.nodes.find((item) => Number(item.id) === Number(nodeId));

            if (!node) return;

            node.position_x = coords.x - 90;
            node.position_y = coords.y - 30;

            this.$wire.updateNodePosition(node.id, node.position_x, node.position_y);
            this.selectNode(node.id, true);
            this.$nextTick(() => this.renderPermanentConnections());
        },

        toggleConnectionMode() {
            this.isConnectionMode = !this.isConnectionMode;
            this.cancelDrawingConnection(false);
        },

        cancelDrawingConnection(resetMode = true) {
            this.isDrawingConnection = false;
            this.drawingFromNodeId = null;
            this.drawStartX = 0;
            this.drawStartY = 0;
            this.drawCurrentX = 0;
            this.drawCurrentY = 0;

            if (resetMode === true && this.isConnectionMode) {
                // Keep connection mode enabled; only cancel active temporary line.
                return;
            }
        },

        startPan(e) {
            this.isPanning = true;
            this.lastMouseX = e.clientX;
            this.lastMouseY = e.clientY;
        },

        onMouseMove(e) {
            if (this.isPanning) {
                this.offsetX += e.clientX - this.lastMouseX;
                this.offsetY += e.clientY - this.lastMouseY;
                this.lastMouseX = e.clientX;
                this.lastMouseY = e.clientY;
                return;
            }

            if (this.isDraggingNode && this.draggedNode && !this.isConnectionMode) {
                this.draggedNode.position_x = parseFloat(this.draggedNode.position_x) + ((e.clientX - this.lastMouseX) / this.zoom);
                this.draggedNode.position_y = parseFloat(this.draggedNode.position_y) + ((e.clientY - this.lastMouseY) / this.zoom);
                this.lastMouseX = e.clientX;
                this.lastMouseY = e.clientY;
                this.renderPermanentConnections();
                return;
            }

            if (this.isDrawingConnection) {
                const coords = this.getCanvasCoords(e.clientX, e.clientY);
                this.drawCurrentX = coords.x;
                this.drawCurrentY = coords.y;
            }
        },

        endMove() {
            if (this.isDraggingNode && this.draggedNode) {
                this.$wire.updateNodePosition(
                    this.draggedNode.id,
                    this.draggedNode.position_x,
                    this.draggedNode.position_y
                );

                this.$nextTick(() => this.renderPermanentConnections());
            }

            this.isPanning = false;
            this.isDraggingNode = false;
            this.draggedNode = null;
        },

        startDragNode(e, node) {
            e.preventDefault();
            e.stopPropagation();

            if (this.isConnectionMode) {
                return;
            }

            this.isDraggingNode = true;
            this.draggedNode = node;
            this.lastMouseX = e.clientX;
            this.lastMouseY = e.clientY;

            this.selectNode(node.id, true);
        },

        async finishConnection(targetNodeId) {
            if (!this.drawingFromNodeId) return;

            if (Number(this.drawingFromNodeId) === Number(targetNodeId)) {
                this.cancelDrawingConnection();
                return;
            }

            const sourceId = this.drawingFromNodeId;

            this.cancelDrawingConnection();

            try {
                const savedConnection = await this.$wire.createVisualConnection(sourceId, targetNodeId);

                if (savedConnection && !this.connections.find((item) => Number(item.id) === Number(savedConnection.id))) {
                    this.connections.push(savedConnection);
                }

                if (savedConnection?.id) {
                    this.selectConnection(savedConnection.id, true);
                }
            } catch (error) {
                console.error('Failed to save knowledge map connection:', error);
            }

            this.$nextTick(() => this.renderPermanentConnections());
        },

        handleNodeMouseUp(e, node) {
            // No action needed here for click-to-click logic
        },

        handleNodeClick(e, node) {
            e.stopPropagation();

            if (this.isConnectionMode) {
                if (this.isDrawingConnection) {
                    this.finishConnection(node.id);
                } else {
                    // Start drawing connection
                    this.isDrawingConnection = true;
                    this.drawingFromNodeId = node.id;

                    // Calculate center point of the node for the start of the line
                    const nodeEl = document.querySelector(`[data-node-id="${node.id}"]`);
                    const rect = nodeEl.getBoundingClientRect();
                    const centerClientX = rect.left + (rect.width / 2);
                    const centerClientY = rect.top + (rect.height / 2);
                    const startCoords = this.getCanvasCoords(centerClientX, centerClientY);

                    this.drawStartX = startCoords.x;
                    this.drawStartY = startCoords.y;
                    this.drawCurrentX = startCoords.x;
                    this.drawCurrentY = startCoords.y;
                }

                return;
            }

            this.selectNode(node.id, true);
        },

        handleNodeDblClick(e, node) {
            e.stopPropagation();

            if (this.isDraggingNode) {
                this.endMove(e);
            }

            this.selectNode(node.id, true);
        },

        selectNode(id, openPanel = true) {
            this.selectedNodeId = Number(id);
            this.selectedConnectionId = null;

            if (openPanel) {
                this.rightPanelOpen = true;
            }

            Promise.resolve(this.$wire.selectNode(id))
                .catch((error) => console.error('Failed to select node:', error))
                .finally(() => this.$nextTick(() => this.renderPermanentConnections()));
        },

        selectConnection(id, openPanel = true) {
            this.selectedConnectionId = Number(id);
            this.selectedNodeId = null;

            if (openPanel) {
                this.rightPanelOpen = true;
            }

            Promise.resolve(this.$wire.selectConnection(id))
                .catch((error) => console.error('Failed to select connection:', error))
                .finally(() => this.$nextTick(() => this.renderPermanentConnections()));
        },

        closeInspector() {
            this.rightPanelOpen = false;
            this.selectedNodeId = null;
            this.selectedConnectionId = null;

            Promise.resolve(this.$wire.deselect())
                .catch((error) => console.error('Failed to deselect:', error));

            this.$nextTick(() => this.renderPermanentConnections());
        },

        deselect() {
            this.closeInspector();
        },

        toggleFocus() {
            this.focusMode = !this.focusMode;

            if (this.focusMode) {
                this.leftPanelOpen = false;
                this.rightPanelOpen = false;
            } else {
                this.leftPanelOpen = true;
            }

            this.$nextTick(() => this.renderPermanentConnections());
        },

        renderPermanentConnections() {
            const layer = this.$refs.permanentConnectionsLayer;

            if (!layer) return;

            layer.innerHTML = '';

            this.connections.forEach((connection) => {
                const fromNode = this.nodes.find((node) => Number(node.id) === Number(connection.from_node_id));
                const toNode = this.nodes.find((node) => Number(node.id) === Number(connection.to_node_id));

                if (!fromNode || !toNode) return;

                const fromPoint = this.getAnchorPoint(fromNode, toNode);
                const toPoint = this.getAnchorPoint(toNode, fromNode);
                const d = this.createConnectionPath(fromPoint, toPoint);
                const isSelected = Number(this.selectedConnectionId) === Number(connection.id);

                const group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                group.classList.add('km-connection-group');
                group.setAttribute('data-connection-id', connection.id);
                group.setAttribute('tabindex', '0');
                group.style.cursor = 'pointer';

                const visiblePath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                visiblePath.setAttribute('d', d);
                visiblePath.setAttribute('fill', 'none');
                visiblePath.setAttribute('stroke', isSelected ? '#3b82f6' : (connection.color || '#6f6a5f'));
                visiblePath.setAttribute('stroke-width', isSelected ? '3.5' : '2.25');
                visiblePath.setAttribute('stroke-linecap', 'round');
                visiblePath.setAttribute('stroke-linejoin', 'round');
                visiblePath.setAttribute('marker-end', isSelected ? 'url(#km-arrow-selected)' : 'url(#km-arrow)');
                visiblePath.classList.add('km-permanent-connection');

                if (isSelected) {
                    visiblePath.classList.add('is-selected');
                    visiblePath.setAttribute('filter', 'drop-shadow(0 0 5px rgba(59, 130, 246, 0.45))');
                }

                if (connection.line_style === 'dashed') {
                    visiblePath.setAttribute('stroke-dasharray', '8 7');
                }

                if (connection.line_style === 'dotted') {
                    visiblePath.setAttribute('stroke-dasharray', '2 7');
                }

                const hitPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                hitPath.setAttribute('d', d);
                hitPath.setAttribute('fill', 'none');
                hitPath.setAttribute('stroke', 'rgba(0,0,0,0.001)');
                hitPath.setAttribute('stroke-width', '32');
                hitPath.setAttribute('stroke-linecap', 'round');
                hitPath.setAttribute('stroke-linejoin', 'round');
                hitPath.setAttribute('pointer-events', 'stroke');
                hitPath.classList.add('km-connection-hit-path');

                group.appendChild(visiblePath);
                group.appendChild(hitPath);

                const selectThisConnection = (event) => {
                    event.preventDefault();
                    event.stopPropagation();

                    this.isPanning = false;
                    this.isDraggingNode = false;
                    this.draggedNode = null;
                    this.justSelectedConnection = true;

                    this.selectConnection(connection.id, true);

                    window.setTimeout(() => {
                        this.justSelectedConnection = false;
                    }, 80);
                };

                group.addEventListener('pointerdown', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    this.isPanning = false;
                });

                group.addEventListener('click', selectThisConnection);

                group.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        selectThisConnection(event);
                    }
                });

                layer.appendChild(group);
            });
        },

        createConnectionPath(from, to) {
            const dx = to.x - from.x;
            const dy = to.y - from.y;
            const distance = Math.sqrt((dx * dx) + (dy * dy));
            const curve = Math.max(70, Math.min(220, distance * 0.35));

            if (Math.abs(dx) >= Math.abs(dy)) {
                const direction = dx >= 0 ? 1 : -1;

                return `M ${from.x} ${from.y} C ${from.x + (curve * direction)} ${from.y}, ${to.x - (curve * direction)} ${to.y}, ${to.x} ${to.y}`;
            }

            const direction = dy >= 0 ? 1 : -1;

            return `M ${from.x} ${from.y} C ${from.x} ${from.y + (curve * direction)}, ${to.x} ${to.y - (curve * direction)}, ${to.x} ${to.y}`;
        },

        getAnchorPoint(node, targetNode) {
            const nodeEl = document.querySelector(`[data-node-id="${node.id}"]`);
            const targetEl = document.querySelector(`[data-node-id="${targetNode.id}"]`);

            const nodeWidth = nodeEl ? nodeEl.offsetWidth : 160;
            const nodeHeight = nodeEl ? nodeEl.offsetHeight : 64;
            const targetWidth = targetEl ? targetEl.offsetWidth : 160;
            const targetHeight = targetEl ? targetEl.offsetHeight : 64;

            const nodeX = Number(node.position_x);
            const nodeY = Number(node.position_y);
            const targetX = Number(targetNode.position_x);
            const targetY = Number(targetNode.position_y);

            const nodeCenterX = nodeX + (nodeWidth / 2);
            const nodeCenterY = nodeY + (nodeHeight / 2);
            const targetCenterX = targetX + (targetWidth / 2);
            const targetCenterY = targetY + (targetHeight / 2);

            const dx = targetCenterX - nodeCenterX;
            const dy = targetCenterY - nodeCenterY;

            if (Math.abs(dx) > Math.abs(dy)) {
                return {
                    x: dx > 0 ? nodeX + nodeWidth : nodeX,
                    y: nodeCenterY,
                };
            }

            return {
                x: nodeCenterX,
                y: dy > 0 ? nodeY + nodeHeight : nodeY,
            };
        },

        getDrawingConnectionPath() {
            if (!this.isDrawingConnection) return '';

            const fromNode = this.nodes.find((node) => Number(node.id) === Number(this.drawingFromNodeId));

            if (!fromNode) return '';

            const from = {
                x: this.drawStartX,
                y: this.drawStartY,
            };

            const to = {
                x: this.drawCurrentX,
                y: this.drawCurrentY,
            };

            return this.createConnectionPath(from, to);
        },

        getSelectedConnectionMidpoint() {
            if (!this.selectedConnectionId) return null;

            const connection = this.connections.find((item) => Number(item.id) === Number(this.selectedConnectionId));

            if (!connection) return null;

            const fromNode = this.nodes.find((node) => Number(node.id) === Number(connection.from_node_id));
            const toNode = this.nodes.find((node) => Number(node.id) === Number(connection.to_node_id));

            if (!fromNode || !toNode) return null;

            const fromPoint = this.getAnchorPoint(fromNode, toNode);
            const toPoint = this.getAnchorPoint(toNode, fromNode);

            return {
                x: (fromPoint.x + toPoint.x) / 2,
                y: (fromPoint.y + toPoint.y) / 2,
            };
        },
    }));
});