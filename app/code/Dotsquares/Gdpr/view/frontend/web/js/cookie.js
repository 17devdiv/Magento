define([
    "jquery",
    "jquery/ui",
    "mage/cookies"
], function ($) {
    'use strict';
    return  {
        init: function init(config) {
            var allowServices = false,
                allowedCookies,
                allowedWebsites;
            allowedCookies = $.mage.cookies.get('gdpr_cookies');
            if (allowedCookies == null) {
                $('.dot_cookies').show();
            } else {
                $('.dot_cookies').hide();
            }
			if (allowedCookies != null && allowedCookies == true) {
				(function (i, s, o, g, r, a, m) {
                    i.GoogleAnalyticsObject = r;
                    i[r] = i[r] || function () {
                    	(i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                    	m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
                ga('create', config.pageTrackingData.accountId, 'auto');
                if (config.pageTrackingData.isAnonymizedIpActive) {
                    ga('set', 'anonymizeIp', true);
                }
                ga('send', 'pageview' + config.pageTrackingData.optPageUrl);
                if (config.ordersTrackingData) {
                    ga('require', 'ec', 'ec.js');
                    ga('set', 'currencyCode', config.ordersTrackingData.currency);
                   	if (config.ordersTrackingData.products) {
                        $.each(config.ordersTrackingData.products, function (index, value) {
                            ga('ec:addProduct', value);
                        });
                    }
                    if (config.ordersTrackingData.orders) {
                        $.each(config.ordersTrackingData.orders, function (index, value) {
                            ga('ec:setAction', 'purchase', value);
                        });
                    }
                    ga('send', 'pageview');
                }
            }
        }
    }
});