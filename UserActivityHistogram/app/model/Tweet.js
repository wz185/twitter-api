Ext.define('UserActivityHistogram.model.Tweet', {
	extend: 'Ext.data.Model',
    fields: [
        {name: 'hour',  type: 'string'},
        {name: 'count', type: 'int'}
    ]
});