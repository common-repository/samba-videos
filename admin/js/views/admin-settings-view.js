var SambaVideosView = wp.Backbone.View.extend({

	id: 'samba-videos-settings',

	events: {
	},

	initialize: function(){
		var self = this;
		self.template = wp.template('samba-videos-settings');

		alert(self.template);
		self.model = new Backbone.Model(settings_data);
		self.render();
	},

	render: function(){
		var self = this;

		//console.log(self.template);
		console.log(self.template({ model: self.model }));

		//self.$el.html(self.template({ model: self.model }) );
	},

	close: function(){
		this.trigger('close:all');
		wp.Backbone.View.apply('close', this);
	}
});


new SambaVideosView();