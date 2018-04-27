/*
 * This file is part of the Fast n' Simple Shop.
 * Copyright (c) 2017 Lukáš Vlček (http://www.vlceklukas.cz)
 *
 */

jQuery(function($) {

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    $('div.ratings a').mouseenter(function (e) {
        var rate = $(this).data('rate');

        if (rate == 2) {
            $('div.ratings a[data-rate="1"]').addClass('active');
        }
        if (rate == 3) {
            $('div.ratings a[data-rate="1"]').addClass('active');
            $('div.ratings a[data-rate="2"]').addClass('active');
        }
        if (rate == 4) {
            $('div.ratings a[data-rate="1"]').addClass('active');
            $('div.ratings a[data-rate="2"]').addClass('active');
            $('div.ratings a[data-rate="3"]').addClass('active');
        }
        if (rate == 5) {
            $('div.ratings a[data-rate="1"]').addClass('active');
            $('div.ratings a[data-rate="2"]').addClass('active');
            $('div.ratings a[data-rate="3"]').addClass('active');
            $('div.ratings a[data-rate="4"]').addClass('active');
        }
    }).mouseleave(function (e) {
        $('div.ratings a[data-rate="1"]').removeClass('active');
        $('div.ratings a[data-rate="2"]').removeClass('active');
        $('div.ratings a[data-rate="3"]').removeClass('active');
        $('div.ratings a[data-rate="4"]').removeClass('active');
    });

});