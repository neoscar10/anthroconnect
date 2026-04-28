document.addEventListener('alpine:init', () => {
    Alpine.data('knowledgeMapBuilder', (config) => ({
        nodes: config.nodes || [],
        connections: config.connections || [],
        zoom: config.zoom || 1,
        canvasWidth: config.canvasWidth || 4000,
        canvasHeight: config.canvasHeight || 3000,
        offsetX: 0,
        offsetY: 0,
        
        // Panel States
        leftPanelOpen: true,
        rightPanelOpen: false,
        focusMode: false,
        
        // Interaction State
        isPanning: false,
        isDraggingNode: false,
        draggedNode: null,
        
        // Connection Mode State
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
        
        init() {
            this.$nextTick(() => {
                this.fitView();
            });
            
            // Listen for Livewire events
            window.addEventListener('km-node-added', (e) => {
                this.nodes.push(e.detail.node);
                this.selectNode(e.detail.node.id);
            });

            window.addEventListener('km-refresh', (e) => {
                const data = e.detail[0] || e.detail;
                if (!data || !data.nodes) return;

                this.nodes = data.nodes.map(n => ({
                    ...n,
                    position_x: parseFloat(n.position_x),
                    position_y: parseFloat(n.position_y)
                }));
                this.connections = data.connections;
                this.isDrawingConnection = false;
                this.drawingFromNodeId = null;
                this.$nextTick(() => {
                    this.renderPermanentConnections();
                });
            });
            
            // Focus mode toggle listener
            window.addEventListener('km-toggle-focus', () => this.toggleFocus());
            
            // Add global spacebar tracking
            window.addEventListener('keydown', (e) => {
                if (e.code === 'Space' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    this.$refs.shell.style.cursor = 'grab';
                }
                if (e.code === 'Escape') {
                    if (this.isConnectionMode) {
                        if (this.isDrawingConnection) {
                            this.isDrawingConnection = false;
                            this.drawingFromNodeId = null;
                        } else {
                            this.isConnectionMode = false;
                        }
                    }
                }
            });
            window.addEventListener('keyup', (e) => {
                if (e.code === 'Space') {
                    this.$refs.shell.style.cursor = this.isConnectionMode ? 'crosshair' : 'default';
                }
            });

            this.$nextTick(() => {
                this.renderPermanentConnections();
            });

            window.addEventListener('resize', () => {
                this.renderPermanentConnections();
            });
        },

        centerCanvas() {
            const shell = this.$refs.shell;
            if (!shell) return;
            // Center based on dynamic canvas size
            this.offsetX = (shell.offsetWidth / 2) - ((this.canvasWidth / 2) * this.zoom);
            this.offsetY = (shell.offsetHeight / 2) - ((this.canvasHeight / 2) * this.zoom);
        },

        // Panning and Zooming
        zoomIn() { 
            const oldZoom = this.zoom;
            this.zoom = Math.min(this.zoom + 0.1, 2.5);
            this.adjustOffsetForZoom(oldZoom);
        },
        zoomOut() { 
            const oldZoom = this.zoom;
            this.zoom = Math.max(this.zoom - 0.1, 0.3);
            this.adjustOffsetForZoom(oldZoom);
        },
        resetZoom() { 
            this.zoom = 1; 
            this.centerCanvas(); 
        },
        fitView() {
            if (this.nodes.length === 0) {
                this.resetZoom();
                return;
            }
            
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
            this.nodes.forEach(n => {
                minX = Math.min(minX, n.position_x);
                minY = Math.min(minY, n.position_y);
                maxX = Math.max(maxX, n.position_x + 180);
                maxY = Math.max(maxY, n.position_y + 80);
            });
            
            const padding = 100;
            const width = (maxX - minX) + (padding * 2);
            const height = (maxY - minY) + (padding * 2);
            const shell = this.$refs.shell;
            
            this.zoom = Math.min(shell.offsetWidth / width, shell.offsetHeight / height, 1);
            this.offsetX = (shell.offsetWidth / 2) - ((minX + (maxX - minX) / 2) * this.zoom);
            this.offsetY = (shell.offsetHeight / 2) - ((minY + (maxY - minY) / 2) * this.zoom);
        },
        
        adjustOffsetForZoom(oldZoom) {
            const shell = this.$refs.shell;
            const centerX = shell.offsetWidth / 2;
            const centerY = shell.offsetHeight / 2;
            
            this.offsetX = centerX - (centerX - this.offsetX) * (this.zoom / oldZoom);
            this.offsetY = centerY - (centerY - this.offsetY) * (this.zoom / oldZoom);
        },

        // Canvas Interactions
        getCanvasCoords(clientX, clientY) {
            const canvasRect = this.$refs.canvas.getBoundingClientRect();
            const x = (clientX - canvasRect.left) / this.zoom;
            const y = (clientY - canvasRect.top) / this.zoom;
            return { x, y };
        },

        handleCanvasClick(e) {
            if (this.isConnectionMode) {
                // Clicking empty canvas in connection mode resets the drawing
                this.isDrawingConnection = false;
                this.drawingFromNodeId = null;
            } else {
                this.deselect();
            }
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
            if (nodeId) {
                const coords = this.getCanvasCoords(e.clientX, e.clientY);
                const node = this.nodes.find(n => n.id == nodeId);
                if (node) {
                    node.position_x = coords.x - 90; // Approx half width of compact node
                    node.position_y = coords.y - 30; // Approx half height
                    
                    this.$wire.updateNodePosition(node.id, node.position_x, node.position_y);
                    this.selectNode(node.id, false);
                }
            }
        },

        toggleConnectionMode() {
            this.isConnectionMode = !this.isConnectionMode;
            this.isDrawingConnection = false;
            this.drawingFromNodeId = null;
        },

        startPan(e) {
            if (e.target.closest('.km-node')) return;
            
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
            } else if (this.isDraggingNode && this.draggedNode && !this.isConnectionMode) {
                this.draggedNode.position_x = parseFloat(this.draggedNode.position_x) + (e.clientX - this.lastMouseX) / this.zoom;
                this.draggedNode.position_y = parseFloat(this.draggedNode.position_y) + (e.clientY - this.lastMouseY) / this.zoom;
                this.lastMouseX = e.clientX;
                this.lastMouseY = e.clientY;
            } else if (this.isDrawingConnection) {
                const coords = this.getCanvasCoords(e.clientX, e.clientY);
                this.drawCurrentX = coords.x;
                this.drawCurrentY = coords.y;
            }
        },

        endMove(e) {
            if (this.isDraggingNode && this.draggedNode) {
                this.$wire.updateNodePosition(
                    this.draggedNode.id, 
                    this.draggedNode.position_x, 
                    this.draggedNode.position_y
                );
                this.$nextTick(() => {
                    this.renderPermanentConnections();
                });
            }
            this.isPanning = false;
            this.isDraggingNode = false;
            this.draggedNode = null;
        },

        startDragNode(e, node) {
            if (this.isConnectionMode) {
                e.stopPropagation();
                if (!this.isDrawingConnection) {
                    // Start drawing connection on mousedown
                    this.isDrawingConnection = true;
                    this.drawingFromNodeId = node.id;
                    
                    const elRect = e.currentTarget.getBoundingClientRect();
                    const centerClientX = elRect.left + (elRect.width / 2);
                    const centerClientY = elRect.top + (elRect.height / 2);
                    
                    const coords = this.getCanvasCoords(centerClientX, centerClientY);
                    this.drawStartX = coords.x;
                    this.drawStartY = coords.y;
                    
                    const mouseCoords = this.getCanvasCoords(e.clientX, e.clientY);
                    this.drawCurrentX = mouseCoords.x;
                    this.drawCurrentY = mouseCoords.y;
                }
                return;
            }
            e.stopPropagation();
            this.isDraggingNode = true;
            this.draggedNode = node;
            this.lastMouseX = e.clientX;
            this.lastMouseY = e.clientY;
            this.selectNode(node.id);
        },

        async finishConnection(targetNodeId) {
            if (!this.drawingFromNodeId) return;

            if (this.drawingFromNodeId === targetNodeId) {
                this.isDrawingConnection = false;
                this.drawingFromNodeId = null;
                return;
            }

            const sourceId = this.drawingFromNodeId;

            // 1. Remove only temporary preview line
            this.isDrawingConnection = false;
            this.drawingFromNodeId = null;

            // 3. Persist connection through Livewire
            const savedConnection = await this.$wire.createVisualConnection(sourceId, targetNodeId);

            // 4. Push saved connection into frontend state (ensure no dupes)
            if (!this.connections.find(c => c.id === savedConnection.id)) {
                this.connections.push(savedConnection);
            }

            // 5. Re-render all permanent connections
            this.$nextTick(() => {
                this.renderPermanentConnections();
            });
        },

        handleNodeMouseUp(e, node) {
            if (this.isConnectionMode && this.isDrawingConnection) {
                e.stopPropagation();
                this.finishConnection(node.id);
            }
        },

        // Node Click Router
        handleNodeClick(e, node) {
            if (this.isConnectionMode) {
                // Clicking a node either starts a drag connection (handled by mousedown)
                // or completes it if it was a distinct click (handled here if we adjust)
                if (this.isDrawingConnection) {
                    this.finishConnection(node.id);
                }
            } else {
                this.selectNode(node.id, false);
            }
        },
        handleNodeDblClick(e, node) {
            if (this.isDraggingNode) {
                this.endMove(e);
            }
        },

        // Selection & Inspector Logic
        selectNode(id, openPanel = false) {
            this.selectedNodeId = id;
            this.selectedConnectionId = null;
            if (openPanel) {
                this.rightPanelOpen = true; 
            }
            this.$wire.selectNode(id);
        },

        selectConnection(id, openPanel = false) {
            this.selectedConnectionId = id;
            this.selectedNodeId = null;
            if (openPanel) {
                this.rightPanelOpen = true; 
            }
            this.$wire.selectConnection(id);
        },
        
        closeInspector() {
            this.rightPanelOpen = false;
            this.selectedNodeId = null;
            this.selectedConnectionId = null;
            this.$wire.deselect();
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
        },

        renderPermanentConnections() {
            const layer = this.$refs.permanentConnectionsLayer;

            if (!layer) return;

            layer.innerHTML = '';

            this.connections.forEach((connection) => {
                const fromNode = this.nodes.find(n => Number(n.id) === Number(connection.from_node_id));
                const toNode = this.nodes.find(n => Number(n.id) === Number(connection.to_node_id));

                if (!fromNode || !toNode) return;

                const fromPoint = this.getAnchorPoint(fromNode, toNode);
                const toPoint = this.getAnchorPoint(toNode, fromNode);

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');

                const d = this.createConnectionPath(fromPoint, toPoint);

                path.setAttribute('d', d);
                path.setAttribute('fill', 'none');
                path.setAttribute('stroke', connection.color || '#6f6a5f');
                path.setAttribute('stroke-width', '2');
                path.setAttribute('marker-end', 'url(#km-arrow)');
                path.setAttribute('data-connection-id', connection.id);
                path.classList.add('km-permanent-connection');

                if (connection.line_style === 'dashed') {
                    path.setAttribute('stroke-dasharray', '7 6');
                }

                if (connection.line_style === 'dotted') {
                    path.setAttribute('stroke-dasharray', '2 6');
                }

                path.addEventListener('click', (event) => {
                    event.stopPropagation();
                    this.selectConnection(connection.id, false);
                });

                layer.appendChild(path);
            });
        },

        createConnectionPath(from, to) {
            const dx = to.x - from.x;
            const curve = Math.max(80, Math.abs(dx) * 0.35);

            return `M ${from.x} ${from.y} C ${from.x + curve} ${from.y}, ${to.x - curve} ${to.y}, ${to.x} ${to.y}`;
        },

        getAnchorPoint(node, targetNode) {
            const nodeEl = document.querySelector(`[data-node-id="${node.id}"]`);
            const targetEl = document.querySelector(`[data-node-id="${targetNode.id}"]`);

            const nodeWidth = nodeEl ? nodeEl.offsetWidth : 150;
            const nodeHeight = nodeEl ? nodeEl.offsetHeight : 56;

            const targetWidth = targetEl ? targetEl.offsetWidth : 150;
            const targetHeight = targetEl ? targetEl.offsetHeight : 56;

            const nodeCenterX = Number(node.position_x) + nodeWidth / 2;
            const nodeCenterY = Number(node.position_y) + nodeHeight / 2;

            const targetCenterX = Number(targetNode.position_x) + targetWidth / 2;
            const targetCenterY = Number(targetNode.position_y) + targetHeight / 2;

            const dx = targetCenterX - nodeCenterX;
            const dy = targetCenterY - nodeCenterY;

            if (Math.abs(dx) > Math.abs(dy)) {
                return {
                    x: dx > 0 ? Number(node.position_x) + nodeWidth : Number(node.position_x),
                    y: nodeCenterY,
                };
            }

            return {
                x: nodeCenterX,
                y: dy > 0 ? Number(node.position_y) + nodeHeight : Number(node.position_y),
            };
        },

        getDrawingConnectionPath() {
            if (!this.isDrawingConnection) return '';
            
            const from = this.nodes.find(n => n.id == this.drawingFromNodeId);
            if (!from) return '';

            // Start from center for active drawing preview
            const nodeEl = document.querySelector(`[data-node-id="${from.id}"]`);
            const fromW = nodeEl ? nodeEl.offsetWidth : 150; 
            const fromH = nodeEl ? nodeEl.offsetHeight : 56;
            
            const x1 = parseFloat(from.position_x) + fromW/2;
            const y1 = parseFloat(from.position_y) + fromH/2;
            
            const x2 = this.drawCurrentX;
            const y2 = this.drawCurrentY;
            
            const dx = Math.abs(x2 - x1) * 0.5;
            return `M ${x1} ${y1} C ${x1 + dx} ${y1}, ${x2 - dx} ${y2}, ${x2} ${y2}`;
        }
    }));
});
