pimcore.registerNS('SocialData.Feed.AbstractFeed');
SocialData.Feed.AbstractFeed = Class.create({

    uId: null,
    connectorEngineId: null,
    wallId: null,
    feedId: null,
    data: null,

    panel: null,

    initialize: function (connectorEngineId, wallId, feedId, data) {
        this.uId = Ext.id();
        this.connectorEngineId = connectorEngineId;
        this.wallId = wallId;
        this.feedId = feedId;
        this.data = data;
    },

    getInternalId: function () {
        return 'feed_' + this.getConnectorEngineId() + '_' + this.uId;
    },

    getConnectorEngineId: function () {
        return this.connectorEngineId;
    },

    getWallId: function () {
        return this.wallId;
    },

    getFeedId: function () {
        return this.feedId;
    },

    /**
     * @abstract
     */
    isValid: function () {
        return false;
    },

    /**
     * @abstract
     */
    getValues: function () {
        return null;
    },
});