window.PaymentGatewayManager = {
    // Dynamically load SDK script if not already loaded
    loadScript(url) {
        return new Promise((resolve, reject) => {
            if (document.querySelector(`script[src="${url}"]`)) {
                resolve();
                return;
            }
            const script = document.createElement('script');
            script.src = url;
            script.onload = () => resolve();
            script.onerror = () => reject(new Error(`Failed to load script: ${url}`));
            document.head.appendChild(script);
        });
    },

    // Launch checkout based on gateway
    async launchCheckout(gateway, options, callbacks) {
        if (gateway === 'razorpay') {
            try {
                await this.loadScript('https://checkout.razorpay.com/v1/checkout.js');
                
                const rzpOptions = {
                    key: options.key,
                    amount: options.amount,
                    currency: options.currency,
                    name: 'AnthroConnect',
                    description: options.meta.title,
                    order_id: options.order_id,
                    prefill: {
                        name: options.user.name,
                        email: options.user.email,
                        contact: options.user.phone
                    },
                    handler: (response) => {
                        // Normalize response
                        const payload = {
                            transaction_reference: options.reference,
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature
                        };
                        callbacks.onSuccess(payload);
                    },
                    modal: {
                        ondismiss: () => {
                            if (callbacks.onDismiss) callbacks.onDismiss();
                        }
                    },
                    theme: {
                        color: '#c2410c'
                    }
                };

                const rzp = new window.Razorpay(rzpOptions);
                rzp.on('payment.failed', (response) => {
                    if (callbacks.onFailure) {
                        callbacks.onFailure(response.error.description || 'Payment failed.');
                    }
                });
                rzp.open();
            } catch (err) {
                if (callbacks.onFailure) {
                    callbacks.onFailure('Failed to initialize Razorpay SDK.');
                }
            }
        } else if (gateway === 'cashfree') {
            try {
                await this.loadScript('https://sdk.cashfree.com/js/v3/2023-08-01/cf.js');

                const cfMode = options.mode === 'live' ? 'production' : 'sandbox';
                const cashfree = window.Cashfree({
                    mode: cfMode
                });

                cashfree.checkout({
                    paymentSessionId: options.meta.payment_session_id,
                    redirectTarget: "_self"
                });
            } catch (err) {
                if (callbacks.onFailure) {
                    callbacks.onFailure('Failed to initialize Cashfree SDK.');
                }
            }
        } else {
            if (callbacks.onFailure) {
                callbacks.onFailure(`Unsupported gateway: ${gateway}`);
            }
        }
    }
};
