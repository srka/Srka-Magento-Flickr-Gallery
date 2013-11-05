/**
 * Main JS
 *
 * @module Srka_Flickrgallery
 */
var Flickrgallery = Class.create();
Flickrgallery.prototype = {
    initialize: function(type, url, result_placeholder_id, has_tooltip, tooltip_style, id_postfix, per_page, thumb_size_prefix) {
        this.type = type;
        this.url = url;
        this.result_placeholder_id = result_placeholder_id;
        this.has_tooltip = has_tooltip;
        this.tooltip_style = tooltip_style;
        this.id_postfix = id_postfix;
        this.per_page = per_page;
        this.thumb_size_prefix = thumb_size_prefix;
        this.preload = $(result_placeholder_id).innerHTML;

        if(this.type == 'photosets'){
            this.initPhotosets();
        }else if(this.type == 'photoset'){
            this.initPhotoset();
        }
    },

    initPhotosets: function() {
        var _this = this;
        var parameters = {};
        if(typeof _this.id_postfix !== 'undefined') parameters.id_postfix = _this.id_postfix;
        if(typeof _this.per_page !== 'undefined') parameters.per_page = _this.per_page;
        new Ajax.Updater(this.result_placeholder_id, this.url, {
            parameters: parameters,
            evalScripts: true,
            onComplete: function (data){

            }
        });
    },

    initPhotoset: function(url) {
        var _this = this;
        if(typeof url !== 'undefined' && url) this.url = url;
        var parameters = {};
        if(typeof _this.id_postfix !== 'undefined') parameters.id_postfix = _this.id_postfix;
        if(typeof _this.per_page !== 'undefined') parameters.per_page = _this.per_page;
        if(typeof _this.thumb_size_prefix !== 'undefined') parameters.thumb_size_prefix = _this.thumb_size_prefix;
        new Ajax.Updater(this.result_placeholder_id, this.url, {
            parameters: parameters,
            evalScripts: true,
            onComplete: function (data){
                if(typeof _this.has_tooltip !== 'undefined' && _this.has_tooltip){
                    $$('.opentip').each(function(elem){
                        var content = elem.readAttribute('title');
                        elem.removeAttribute('title');
                        if(typeof _this.tooltip_style !== 'undefined' && _this.tooltip_style){
                            $(elem.readAttribute('id')).addTip(
                                content, {
                                    className: _this.tooltip_style,
                                    delay: 0.1,
                                    stem: false,
                                    target: false,
                                    tipJoint: ['center', 'top']
                                });
                        }else{
                            $(elem.readAttribute('id')).addTip(
                                content, {
                                    delay: 0.1,
                                    stem: false,
                                    target: false,
                                    tipJoint: ['center', 'top']
                                });
                        }
                    });
                }

            }
        });
    },

    initPhotosetCustom: function(url){
        $$('#' + this.result_placeholder_id + ' .loading-mask-wrapper.custom')[0].addClassName('show');
        this.initPhotoset(url);
    },

    addPhotosetCrumb: function(photoset_name){
        if($$('.breadcrumbs').length){
            $$('.breadcrumbs li.Gallery')[0].insert('<span>/ </span>');
            $$('.breadcrumbs ul')[0].insert('<li class="' + photoset_name + '"><strong>' + photoset_name + '</strong></li>');
        }
    }
};

var FlickrgalleryCarousel = Class.create();
FlickrgalleryCarousel.prototype = {
    initialize: function(url, result_placeholder_id, has_tooltip, tooltip_style, options){
        this.url = url;
        this.result_placeholder_id = result_placeholder_id;
        this.options = options;

        new Ajax.Updater(result_placeholder_id, url, {
            evalScripts: true,
            onComplete: function (data){
                new Carousel('flickrgallery-carousel-wrapper', $$("#flickrgallery-carousel-content a"), $('flickrgallery-carousel').select('.carousel-control'), options);

                if(typeof has_tooltip !== 'undefined' && has_tooltip){
                    $$('.opentip').each(function(elem){
                        var content = elem.readAttribute('title');
                        elem.removeAttribute('title');
                        if(typeof tooltip_style !== 'undefined' && tooltip_style){
                            $(elem.readAttribute('id')).addTip(
                                content, {
                                    className: tooltip_style,
                                    delay: 0.1,
                                    stem: false,
                                    target: false,
                                    tipJoint: ['center', 'top']
                                });
                        }else{
                            $(elem.readAttribute('id')).addTip(
                                content, {
                                    delay: 0.1,
                                    stem: false,
                                    target: false,
                                    tipJoint: ['center', 'top']
                                });
                        }
                    });
                }

            }
        });
    }
};