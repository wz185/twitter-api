Ext.define('UserActivityHistogram.store.Tweet', {
	extend: 'Ext.data.Store',
	requires: [
		'UserActivityHistogram.model.Tweet'
	],

	model: 'UserActivityHistogram.model.Tweet',
	proxy: {
	    type: 'ajax',
//	    url: 'resources/tweets.json',
        url: 'resources/get-last-500-tweets-by-user.php',
	    reader: {
	        type: 'json'
	    }
	}
});