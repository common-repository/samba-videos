/**
 *
 * @link       https://github.com/jefmoura
 * @since      1.0.0
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/admin/js
 */

 /**
 * The Samba Videos Media menu specific functionality of the plugin.
 *
 *
 * @package    Samba_Videos
 * @subpackage Samba_Videos/admin/js
 * @author     Jeferson Moura <jeferson.moura@sambatech.com.br>, Fagner Valente <fagner.valente@sambatech.com.br>
 */

wp.media.controller.Custom = wp.media.controller.State.extend({

    initialize: function(){
        // this model contains all the relevant data needed for the application
        this.props = new Backbone.Model({
        	selectedMedia: null,
        	totalLoaded: 0,
        	totalMedias: 0
        });
        this.props.on( 'change', this.refresh, this );

    },
    
    // called each time the model changes
    refresh: function() {
        // update the toolbar
    	this.frame.toolbar.get().refresh();
	},
	
	// called when the toolbar button is clicked
	insertShortCode: function(){
	    wp.media.editor.insert(SV.getShortCode(this.props.get('selectedMedia').get('id'), this.frame.content.get().mediaPreview.getCustomValues() ));
	    this.props.set('selectedMedia', null);
	    this.frame.close();
	},

	backToGallery: function(){
		var mediaPreview = this.frame.content.get().mediaPreview;

		if ( mediaPreview ){
			mediaPreview.remove();
			this.props.set('selectedMedia', null);
		}
	},

	showMore: function(){
		this.frame.content.get().showMore();
	}
    
});

// custom toolbar : contains the buttons at the bottom
wp.media.view.Toolbar.Custom = wp.media.view.Toolbar.extend({
	initialize: function() {
		this.$el.addClass('sv-toolbar-bottom');
		_.defaults( this.options, {
		    event: 'select_media',
		    close: false,
			items: {
			    select_media: {
			        text: wp.media.view.l10n.selectMedia, // added via 'media_view_strings'
			        style: 'primary',
			        priority: 80,
			        requires: false,
			        click: function(){ 
			        	this.controller.state().insertShortCode();
			        }
			    },
			    back_to_gallery: {
			    	text: wp.media.view.l10n.backToGallery,
			    	style: 'default hidden',
			    	priority: 80,
			    	requires: false,
			    	click: function(e){
			    		this.controller.state().backToGallery();
			    	}
			    },
			    show_more: {
			    	text: wp.media.view.l10n.showMore,
			        style: 'default hidden button-show-more',
			        priority: 80,
			        requires: false,
			        click: function(){
			        	this.controller.state().showMore();
			        }
			    }
			}
		});

		wp.media.view.Toolbar.prototype.initialize.apply( this, arguments );

	},

    // called each time the model changes
	refresh: function() {
	    // you can modify the toolbar behaviour in response to user actions here
	    // disable the button if there is no custom data
	    var self 			= this;
	    var props 			= this.controller.state().props;
		var selectedMedia 	= props.get('selectedMedia');

		//Logic Show/Hide - Select Media
		this.get('select_media').model.set( 'disabled', !selectedMedia );

		//Logic Show/Hide - Back To Gallery
		this.get('back_to_gallery').model.set( 'style', !selectedMedia ? 'default hidden' : 'default');

		//Logic Show/Hide - Show More
		if ( !selectedMedia && props.get('totalLoaded') < props.get('totalMedias') ){
			this.get('show_more').$el.removeClass('hidden');
		}else{
			this.get('show_more').$el.addClass('hidden');
		}
		
	    // call the parent refresh
		wp.media.view.Toolbar.prototype.refresh.apply( this, arguments );
	},
	
	// triggered when the button is clicked
	customAction: function(){
	    this.controller.state().customAction();
	}

});

var SVCollection = Backbone.Collection.extend({

	page: 0, 

	initialize: function(){
		this.totalMedias = 0;
	},

	parse: function(response){
		var body = {};

		try{
			body = JSON.parse(response.body);
		}catch(e){
			console.error("Error API reponse ", e, response.body);
		}
		
		this.totalMedias = parseInt(response.headers.totalmedias);
		return body;
	},
	fetch: function(options){

		this.page = options.reset ? 0 : this.page + 1;

		options.data.start = this.page;

		return Backbone.Collection.prototype.fetch.call(this, options);
	}
});

// custom content : this view contains the main panel UI
wp.media.view.Custom = wp.media.View.extend({

	
	className: 'attachments-browser',
	
	// bind view events
	events: {
		'keyup .search'			: 'keyupSearch',
		'click .button-filter'	: 'filter'
	},

	initialize: function() {

	    this.collection = new SVCollection();
	    this.mediaItemViews = [];

	    this.fetchMedias();

	    this.template = wp.template('samba-videos-media-list');

	    this.on('select:media', this.showMediaPreview, this);

	    this.render();
	},
	
	render: function(){

	    this.$el.html(this.template());
	    return this;
	},

	showMediaPreview: function(media){

		this.controller.state().props.set('selectedMedia', media);

		this.mediaPreview = new SVMediaPreview({
			model: media,
			container: this.$el,
			parent: this
		});
	},

	fetchMedias: function(params, showMore){
		var self = this;

		var staticParams = {
			action: 'proxy_sv_request',
			type: 'VIDEO'
		};

		var params = jQuery.extend({}, params, staticParams);

		self.$('.sv-loading').removeClass('hidden');

		var oldTotalLoaded = showMore ? self.collection.length : 0;

		if ( !showMore )
			self.trigger('reset:medias');

		self.collection.fetch({
			url: ajaxurl,
			type: 'POST',
			reset: !showMore,
			remove: !showMore,
			data: params,
			crossDomain: true,
			success: function(data, response, options){

				self.controller.state().props.set({
					totalMedias: self.collection.totalMedias,
					totalLoaded: self.collection.length
				});

				if ( self.collection.length > 0 ){
					self.$el.find('.no-content').addClass('hidden');
				}else{
					self.$el.find('.no-content').removeClass('hidden');
				}

				self.mediaItemViews = [];

				for (var i = oldTotalLoaded, total = self.collection.length; i < total; i++) {
					
					var mediaView = new SVMediaItem({
						model: self.collection.models[i],
						parent: self
					});

					self.mediaItemViews.push(mediaView);
					mediaView.render();
					self.$('ul#sv-media-list').append( mediaView.el );

				}

				self.$('.sv-loading').addClass('hidden');
			},
			error: function(resp, xhr){
				console.error(resp, xhr);
			}
		});

	},

	keyupSearch: function(e){
		if (e.keyCode == 13) {
        	this.filter();
    	}
	},

	filter: function() {
		this.fetchMedias(this.getFilters(), false);
	},

	getFilters: function(){
		$tollbar = this.$('.media-toolbar');

		var term = $tollbar.find('input[name="search"]').val();
		var params = {};

		if (term != ''){
			params.search = term
		}

		return params;
	},

	showMore: function(){
		this.fetchMedias(this.getFilters(), true);
	}
	
});

var SVMediaItem = wp.Backbone.View.extend({

	tagName   : 'li',

    className : 'attachment sv-item',

	events: {
		'click .attachment-preview':  	"clickMedia"
	},

	initialize: function(){
		this.$el.attr('data-id', this.model.get('id'));
		this.template = wp.template('samba-videos-media-item');
		this.options.parent.on('reset:medias', this.remove , this);
		this.model.on('remove', this.remove, this);
	},

	render: function(){
		this.$el.html(this.template( { media: this.model.toJSON() } ) );
		return this;
	},

	clickMedia: function(){
		this.options.parent.trigger('select:media', this.model);
	},

	remove: function(){
		this.options.parent.off(null, null, this);
		wp.Backbone.View.prototype.remove.apply(this, arguments);
	}

});

var SVMediaPreview = wp.Backbone.View.extend({
	tagName: 'div',

	className: 'sv-media-preview',

	events: {},

	initialize: function(){
		this.template = wp.template('samba-videos-media-preview');
		this.render();
		this.options.parent.trigger('show:preview', this.model);
	},

	render: function(){

		this.$el.html(this.template({
			media: this.model.toJSON(),
			settings: SV.CONFIG
		}));

		this.options.container.append(this.el);
	},

	getCustomValues: function(){
		var result = {};

		result.width = this.$('input[name="sv_width"]').val();
		result.height = this.$('input[name="sv_height"]').val();
		result.autoStart = this.$('input[name="sv_autoStart"]').is(':checked');

		return result;
	}

});


// supersede the default MediaFrame.Post view
var oldMediaFrame = wp.media.view.MediaFrame.Post;
wp.media.view.MediaFrame.Post = oldMediaFrame.extend({

    initialize: function() {
        oldMediaFrame.prototype.initialize.apply( this, arguments );
        
        this.states.add([
            new wp.media.controller.Custom({
                id:         'my-action',
                menu:       'default', // menu event = menu:render:default
                content:    'custom',
				title:      wp.media.view.l10n.sambaVideosGallery, // added via 'media_view_strings'
				priority:   200,
				toolbar:    'main-my-action', // toolbar event = toolbar:create:main-my-action
				type:       'link'
            })
        ]);

        this.on( 'content:render:custom', this.customContent, this );
        this.on( 'toolbar:create:main-my-action', this.createCustomToolbar, this );
        this.on( 'toolbar:render:main-my-action', this.renderCustomToolbar, this );
    },
    
    createCustomToolbar: function(toolbar){
        toolbar.view = new wp.media.view.Toolbar.Custom({
		    controller: this
	    });
    },

    customContent: function(){
        
        // this view has no router
        this.$el.addClass('hide-router');

        // custom content view
        var view = new wp.media.view.Custom({
            controller: this,
            model: this.state().props
        });

        this.content.set( view );
	    this.$el.find('.media-frame-content').attr('data-columns', 6);
    }

});