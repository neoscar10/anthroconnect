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

        init() {
            console.log('Knowledge Map initialized with:', {
                nodes: this.nodes.length,
                connections: this.connections.length
            });
            this.$nextTick(() => {
                this.renderConnections();
                this.centerInitialView();
            });

            window.addEventListener('resize', () => this.renderConnections());

            Livewire.on('knowledge-map-data-updated', (payload) => {
                this.nodes = payload.nodes || [];
                this.connections = payload.connections || [];
                this.$nextTick(() => this.renderConnections());
            });
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
            const curve = Math.max(70, Math.abs(dx) * 0.35);
            return `M ${from.x} ${from.y} C ${from.x + curve} ${from.y}, ${to.x - curve} ${to.y}, ${to.x} ${to.y}`;
        },

        getAnchorPoint(node, targetNode) {
            const nodeEl = document.querySelector(`[data-node-id="${node.id}"]`);
            const width = nodeEl ? nodeEl.offsetWidth : 150;
            const height = nodeEl ? nodeEl.offsetHeight : 50;

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
            this.zoom = Math.min(this.zoom + 0.1, 2);
            this.applyTransform();
        },

        zoomOut() {
            this.zoom = Math.max(this.zoom - 0.1, 0.5);
            this.applyTransform();
        },

        resetView() {
            this.zoom = 1;
            this.offsetX = 0;
            this.offsetY = 0;
            this.applyTransform();
        },

        fitView() {
            if (this.nodes.length === 0) {
                this.resetView();
                return;
            }
            
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
            this.nodes.forEach(n => {
                const x = Number(n.position_x);
                const y = Number(n.position_y);
                minX = Math.min(minX, x);
                minY = Math.min(minY, y);
                maxX = Math.max(maxX, x + 180);
                maxY = Math.max(maxY, y + 80);
            });
            
            const padding = 60;
            const width = (maxX - minX) + (padding * 2);
            const height = (maxY - minY) + (padding * 2);
            const container = this.$refs.canvas;
            
            if (!container) return;

            const targetZoom = Math.min(container.offsetWidth / width, container.offsetHeight / height, 1);
            this.zoom = Math.max(targetZoom, 0.5);
            
            this.offsetX = (container.offsetWidth / 2) - ((minX + (maxX - minX) / 2) * this.zoom);
            this.offsetY = (container.offsetHeight / 2) - ((minY + (maxY - minY) / 2) * this.zoom);
            
            this.applyTransform();
            this.renderConnections();
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
