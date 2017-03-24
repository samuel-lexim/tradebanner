/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
        [
            'J2t_Rewardpoints/js/view/summary/rewardpoints'
        ],
        function (Component) {
            "use strict";
            return Component.extend({
                defaults: {
                    template: 'J2t_Rewardpoints/cart/totals/rewardpoints'
                },
                /**
                 * @override
                 *
                 * @returns {boolean}
                 */
                isDisplayed: function () {
                    return this.getPureValue() != 0;
                }
            });
        }
);
