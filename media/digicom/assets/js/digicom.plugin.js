(function ($, window, document, undefined) {

	// Create the defaults once
	var pluginName = "digicom";

	var defaults = {

	};

	// The actual plugin constructor
	function Plugin(element, options) {
		this.element = element;
		this.options = $.extend({}, defaults, options);
		this._defaults = defaults;

		// Selector values
		this._name = pluginName;

		this.init();
	}

	Plugin.prototype = {
		init: function () {
			var self = this;

			// IE < 9 - Avoid to submit placeholder value
			if(!document.addEventListener  ) {
				if (this.searchField.val() === this.searchField.attr('placeholder')) {
					this.searchField.val('');
				}
			}

			// Get values
			this.searchString = this.searchField.val();

		},
		toggleList: function () {
			this.toggleContainer(this.listContainer);

			if (this.listContainer.hasClass('shown')) {
				this.listButton.addClass('btn-primary');
			} else {
				this.listButton.removeClass('btn-primary');
			}
		}
	};

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function (options) {
		return this.each(function () {
			if (!$.data(this, "plugin_" + pluginName)) {
				$.data(this, "plugin_" + pluginName, new Plugin(this, options));
			}
		});
	};

})(jQuery, window, document);
