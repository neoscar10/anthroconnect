document.addEventListener('alpine:init', () => {
    Alpine.data('knowledgeMapUserCanvas', ({ nodes, connections, selectedNodeId, canvasWidth, canvasHeight }) => ({
        nodes: nodes || [],
        connections: connections || [],
        selectedNodeId: selectedNodeId || null,
        canvasWidth: canvasWidth || 4000,
        canvasHeight: canvasHeight || 3000,
        zoom: 1,
        offsetX: 0,
        offsetY: 0,
        isPanning: false,
        lastMouseX: 0,
        lastMouseY: 0,

        init() {
            this.$nextTick(() => {
                this.renderConnections();
                this.centerInitialView();
            });

            window.addEventListener('resize', () => {
                window.requestAnimationFrame(() => this.renderConnections());
            });

            Livewire.on('knowledge-map-data-updated', (payload) => {
                const data = payload[0] || payload;
                this.nodes = data.nodes || [];
                this.connections = data.connections || [];
                this.$nextTick(() => this.renderConnections());
            });
        },

        handleMouseDown(e) {
            if (e.target.closest('.km-map-node') || e.target.closest('.km-controls')) return;
            this.isPanning = true;
            this.lastMouseX = e.clientX;
            this.lastMouseY = e.clientY;
        },

        handleMouseMove(e) {
            if (!this.isPanning) return;
            
            this.offsetX += e.clientX - this.lastMouseX;
            this.offsetY += e.clientY - this.lastMouseY;
            this.lastMouseX = e.clientX;
            this.lastMouseY = e.clientY;
            
            this.applyTransform();
        },

        handleMouseUp() {
            this.isPanning = false;
        },

        selectNode(id) {
            this.selectedNodeId = id;
            this.$wire.selectNode(id);
            this.$nextTick(() => this.renderConnections());
        },

        renderConnections() {
            const layer = this.$refs.connectionsLayer;
            if (!layer) return;

            layer.innerHTML = '';
            
            this.connections.forEach((connection) => {
                const fromNode = this.nodes.find(n => Number(n.id) === Number(connection.from_node_id));
                const toNode = this.nodes.find(n => Number(n.id) === Number(connection.to_node_id));

                if (!fromNode || !toNode) return;

                const from = this.getAnchorPoint(fromNode, toNode);
                const to = this.getAnchorPoint(toNode, fromNode);

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', this.createPath(from, to));
                path.setAttribute('class', 'km-line');
                layer.appendChild(path);
            });
        },

        createPath(from, to) {
            const dx = to.x - from.x;
            const dy = to.y - from.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            const curve = Math.max(70, Math.min(200, distance * 0.35));
            
            if (Math.abs(dx) > Math.abs(dy)) {
                return `M ${from.x} ${from.y} C ${from.x + (dx > 0 ? curve : -curve)} ${from.y}, ${to.x - (dx > 0 ? curve : -curve)} ${to.y}, ${to.x} ${to.y}`;
            } else {
                return `M ${from.x} ${from.y} C ${from.x} ${from.y + (dy > 0 ? curve : -curve)}, ${to.x} ${to.y - (dy > 0 ? curve : -curve)}, ${to.x} ${to.y}`;
            }
        },

        getAnchorPoint(node, targetNode) {
            const nodeEl = document.querySelector(`[data-node-id="${node.id}"]`);
            const width = nodeEl ? nodeEl.offsetWidth : 160;
            const height = nodeEl ? nodeEl.offsetHeight : 60;

            const nodeX = Number(node.position_x || 0);
            const nodeY = Number(node.position_y || 0);
            const targetX = Number(targetNode.position_x || 0);
            const targetY = Number(targetNode.position_y || 0);

            const nodeCenterX = nodeX + width / 2;
            const nodeCenterY = nodeY + height / 2;
            const targetCenterX = targetX + width / 2;
            const targetCenterY = targetY + height / 2;

            const dx = targetCenterX - nodeCenterX;
            const dy = targetCenterY - nodeCenterY;

            if (Math.abs(dx) > Math.abs(dy)) {
                return {
                    x: dx > 0 ? nodeX + width : nodeX,
                    y: nodeCenterY
                };
            }

            return {
                x: nodeCenterX,
                y: dy > 0 ? nodeY + height : nodeY
            };
        },

        zoomIn() {
            const oldZoom = this.zoom;
            this.zoom = Math.min(this.zoom + 0.15, 2.5);
            this.adjustOffsetForZoom(oldZoom);
            this.applyTransform();
        },

        zoomOut() {
            const oldZoom = this.zoom;
            this.zoom = Math.max(this.zoom - 0.15, 0.2);
            this.adjustOffsetForZoom(oldZoom);
            this.applyTransform();
        },

        adjustOffsetForZoom(oldZoom) {
            const container = this.$refs.canvas;
            if (!container || oldZoom === this.zoom) return;

            const centerX = container.offsetWidth / 2;
            const centerY = container.offsetHeight / 2;

            this.offsetX = centerX - ((centerX - this.offsetX) * (this.zoom / oldZoom));
            this.offsetY = centerY - ((centerY - this.offsetY) * (this.zoom / oldZoom));
        },

        fitView() {
            if (this.nodes.length === 0) return;
            
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
            this.nodes.forEach(n => {
                const x = Number(n.position_x);
                const y = Number(n.position_y);
                minX = Math.min(minX, x);
                minY = Math.min(minY, y);
                maxX = Math.max(maxX, x + 200);
                maxY = Math.max(maxY, y + 80);
            });
            
            const padding = 100;
            const width = (maxX - minX) + (padding * 2);
            const height = (maxY - minY) + (padding * 2);
            const container = this.$refs.canvas;
            
            if (!container) return;

            const targetZoom = Math.min(container.offsetWidth / width, container.offsetHeight / height, 1.2);
            this.zoom = Math.max(targetZoom, 0.3);
            
            this.offsetX = (container.offsetWidth / 2) - ((minX + (maxX - minX) / 2) * this.zoom);
            this.offsetY = (container.offsetHeight / 2) - ((minY + (maxY - minY) / 2) * this.zoom);
            
            this.applyTransform();
            this.$nextTick(() => this.renderConnections());
        },

        centerInitialView() {
            this.fitView();
        },

        applyTransform() {
            const nodesLayer = this.$refs.nodesLayer;
            const svg = this.$refs.svg;

            if (nodesLayer) {
                nodesLayer.style.transform = `translate(${this.offsetX}px, ${this.offsetY}px) scale(${this.zoom})`;
                nodesLayer.style.transformOrigin = '0 0';
            }

            if (svg) {
                svg.style.transform = `translate(${this.offsetX}px, ${this.offsetY}px) scale(${this.zoom})`;
                svg.style.transformOrigin = '0 0';
            }
        }
    }));
});
