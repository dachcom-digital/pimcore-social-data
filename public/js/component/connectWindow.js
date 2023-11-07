pimcore.registerNS('SocialData.Component.ConnectWindow');
SocialData.Component.ConnectWindow = Class.create({

    stateData: null,

    loginWindow: null,
    loginTimer: null,

    connectionUrl: null,
    onError: null,
    onSuccess: null,
    onClose: null,

    initialize: function (connectionUrl, onSuccess, onError, onClose) {

        this.stateData = null;
        this.connectionUrl = connectionUrl;
        this.onSuccess = typeof onSuccess === 'function' ? onSuccess : null;
        this.onError = typeof onError === 'function' ? onError : null;
        this.onClose = typeof onClose === 'function' ? onClose : null;
    },

    open: function () {

        var windowSize, windowLocation, features;

        windowSize = {
            width: 800,
            height: 550,
        };

        windowLocation = {
            left: ((window.screenLeft ? window.screenLeft : window.screenX) + (window.innerWidth / 2)) - (windowSize.width / 2),
            top: ((window.screenTop ? window.screenTop : window.screenY) + (window.screen.availHeight / 2)) - (window.innerHeight / 2)
        };

        features = [
            'toolbar=1',
            'location=1',
            'width=' + windowSize.width,
            'height=' + windowSize.height,
            'left=' + windowLocation.left,
            'top=' + windowLocation.top,
        ];

        this.loginWindow = window.open(window.location.origin + this.connectionUrl, 'LoginWindow', features.join(','));
        this.loginTimer = setInterval(this.checkLoginWindowClosure.bind(this), 500);
    },

    checkLoginWindowClosure: function () {

        var stateElement,
            popupDocument;

        if (!this.loginWindow) {
            return;
        }

        if (this.stateData !== null) {

            this.loginWindow.close();

            clearInterval(this.loginTimer);

            if (this.stateData.error === true) {

                if (this.onError !== null) {
                    this.onError(this.stateData);
                }

                return;
            }

            if (this.onSuccess !== null) {
                this.onSuccess(this.stateData);
            }

            return;

        } else if (this.loginWindow.closed) {

            clearInterval(this.loginTimer);

            if (this.onClose !== null) {
                this.onClose();
            }

            return;
        }

        try {
            popupDocument = this.loginWindow.document;
        } catch (error) {
            return;
        }

        if (popupDocument.domain !== document.domain) {
            return;
        }

        try {
            stateElement = popupDocument.getElementById('connect-response');
        } catch (error) {
            return;
        }

        if (stateElement) {
            this.stateData = Ext.decode(stateElement.value);
        }
    }
});