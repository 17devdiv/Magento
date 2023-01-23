define(
    [
        'ko',
        'uiComponent',
		'jquery'
    ],
    function (ko, Component,$) {
        "use strict";
		var isDisplayed = false;
		var gdpr_enable = window.checkoutConfig.gdpr_enable;
		if(gdpr_enable == 1){
			isDisplayed = true;
		}
		return Component.extend({
            defaults: {
                template: 'Dotsquares_Gdpr/gdpr'
            },
            isRegisterNewsletter: false,
			isDisplayed:isDisplayed,
			initObservable: function () {
                this._super()
                .observe({
                        isRegisterNewsletter: ko.observable(false)                        
                });
                this.isRegisterNewsletter.subscribe(function (newValue) {
                    if(newValue){
                        $("#gdpr-error").hide();
                    }else{
                        $("#gdpr-error").show();
                    }
                });
				return this;
			},
			initialize: function () {
                $(function() {
                    $('body').on("click", '.continue', function () {
                        if($("#place-order-newsletter").prop('checked')==false){
							$("#gdpr-error").show();
							return false;
						}
                    });
                });
                var self = this;
                this._super();
            },
			gdprcheckobxmassage: function () {
                return window.checkoutConfig.gdprcheckobxmassage;
			}
		});
    });