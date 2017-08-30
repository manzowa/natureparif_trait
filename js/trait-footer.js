(function($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.baseTrait = {
        attach: function(context, settings) {

            var selectDom = {
                taxonomie: $('#edit-group-taxonomique'),
                famille: $('#edit-familles'),
                espece: $('#edit-especes'),
                trait: $('#edit-traits'),
                modalite: $('#edit-modalites'),
                source: $('#edit-sources'),
                baseUrl: function() {
                    var origin = window.location.origin;
                    var path = window.location.pathname;
                    var listes = path.split("/", 3);
                    var url = origin + "/" + listes[1] + "/" + listes[2];
                    return url;
                }
            };
            $(selectDom.famille).addClass("form-autocomplete ui-autocomplete-input"); // ui-widget 
            $('#edit-especes').addClass("form-autocomplete "); // ui-widget
            $('.form-item-modalites').hide();
            $('.form-item-sources').hide();

            if ($('.form-item-traits').hide()) {
                $('.form-item-modalites').hide();
                $('.form-item-sources').hide();
            }


            /**
             * Event keyup
             * autocomplete
             */
            selectDom.famille.keyup(function(e) {
                var selectors = {
                    taxonomie: ($('#edit-group-taxonomique').val() !== "") ? $('#edit-group-taxonomique').val() : "rien",
                };
                $("#edit-especes").val(""); // Reset espese
                $('.form-item-modalites').hide(); // hide modalite
                $('.form-item-sources').hide(); // hide sources
                $(selectDom.famille).autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: selectDom.baseUrl() + "/" + selectors.taxonomie,
                            dataType: "json",
                            data: { q: request.term },
                            success: function(data) { response(data); }
                        });
                    }
                });
                e.preventDefault();
            });

            /**
             * Event keyup
             * autocomplete
             */
            selectDom.espece.keyup(function(e) {
                var selectors = {
                    taxonomie: ($('#edit-group-taxonomique').val() !== "") ? $('#edit-group-taxonomique').val() : "nothing",
                    famille: ($('#edit-familles').val() !== "") ? $('#edit-familles').val() : "nothing",
                };
                $('.form-item-modalites').hide();
                $('.form-item-sources').hide();
                $(selectDom.espece).autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: selectDom.baseUrl() + "/" + selectors.taxonomie + '/' + selectors.famille,
                            dataType: "json",
                            data: { q: request.term },
                            success: function(data) { response(data); }
                        });
                    },
                    minLength: 2
                });
                e.preventDefault();
            });

            /**
             * Event change taxonomie
             */
            selectDom.taxonomie.once().on("change", function(e) {
                var selectors = {
                    taxonomie: ($('#edit-group-taxonomique').val() !== "") ? $('#edit-group-taxonomique').val() : "rien",
                    famille: ($('#edit-familles').val() !== "") ? $('#edit-familles').val() : "rien",
                    espece: ($('#edit-especes').val() !== "") ? $('#edit-especes').val() : "rien",
                    trait: ($('#edit-traits').val() !== "") ? $('#edit-traits').val() : "rien",
                    modalite: ($('#edit-modalites').val() !== "") ? $('#edit-modalites').val() : "rien",
                };
                var path = {
                    trait: selectDom.baseUrl() + "/" + selectors.taxonomie + '/' + selectors.famille + '/' + selectors.espece,
                    modalite: selectDom.baseUrl() + "/" + selectors.taxonomie + '/' + selectors.famille + '/' + selectors.espece + '/' + selectors.trait,
                    source: selectDom.baseUrl() + "/" + selectors.taxonomie + '/' + selectors.famille + '/' + selectors.espece + '/' + selectors.trait + '/' + selectors.modalite
                };

                // Reset all input
                $("#edit-familles").val("");
                $("#edit-especes").val("");

                if ($("edit-traits").empty()) {
                    $('.form-item-modalites').hide();
                    $('.form-item-sources').hide();
                }


                // trait
                $("#edit-traits").empty(); //To reset traits
                var edit_trait = $('#edit-traits');
                edit_trait.html("<option value selected='selected'>- select -</option>");
                $.getJSON(path.trait, function(data) {
                    $.each(data, function(i, item) {
                        var option = new Option(item, item);
                        edit_trait.append($(option));
                    });
                });

                e.preventDefault();
            });

            /**
             * Event change famille
             */
            selectDom.famille.once().on("change", function(e) {
                var selectors = {
                    taxonomie: ($('#edit-group-taxonomique').val() !== "") ? $('#edit-group-taxonomique').val() : "rien",
                    famille: ($('#edit-familles').val() !== "") ? $('#edit-familles').val() : "rien",
                    espece: ($('#edit-especes').val() !== "") ? $('#edit-especes').val() : "rien",
                    trait: ($('#edit-traits').val() !== "") ? $('#edit-traits').val() : "rien",
                    modalite: ($('#edit-modalites').val() !== "") ? $('#edit-modalites').val() : "rien",
                };

                var path = {
                    trait: selectDom.baseUrl() + "/" + selectors.taxonomie + '/' + selectors.famille + '/' + selectors.espece,
                };

                // trait
                $("#edit-traits").empty(); //To reset traits
                var edit_trait = $('#edit-traits');
                edit_trait.html("<option value selected='selected'>- select -</option>");
                $.getJSON(path.trait, function(data) {
                    $.each(data, function(i, item) {
                        var option = new Option(item, item);
                        edit_trait.append($(option));
                    });
                });
                e.preventDefault();
            });

            /**
             * Event change famille
             */
            selectDom.espece.once().on("change", function(e) {
                var selectors = {
                    taxonomie: ($('#edit-group-taxonomique').val() !== "") ? $('#edit-group-taxonomique').val() : "rien",
                    famille: ($('#edit-familles').val() !== "") ? $('#edit-familles').val() : "rien",
                    espece: ($('#edit-especes').val() !== "") ? $('#edit-especes').val() : "rien",
                    trait: ($('#edit-traits').val() !== "") ? $('#edit-traits').val() : "rien",
                    modalite: ($('#edit-modalites').val() !== "") ? $('#edit-modalites').val() : "rien",
                };

                var path = {
                    trait: selectDom.baseUrl() + "/" + selectors.taxonomie + '/' + selectors.famille + '/' + selectors.espece,
                };

                // trait
                $("#edit-traits").empty(); //To reset traits
                var edit_trait = $('#edit-traits');
                edit_trait.html("<option value selected='selected'>- select -</option>");
                $.getJSON(path.trait, function(data) {
                    $.each(data, function(i, item) {
                        var option = new Option(item, item);
                        edit_trait.append($(option));
                    });
                });

            });

            /**
             * Change Trait
             */
            selectDom.trait.on("change", function(e) {
                var etat = $(this).val() == "" ? true : false;
                if (etat) {
                    $('.form-item-modalites').hide();
                    $('.form-item-sources').hide();
                } else {
                    $('.form-item-modalites').show();
                    $('.form-item-sources').show();
                }
                var selectors = {
                    taxonomie: ($('#edit-group-taxonomique').val() !== "") ? $('#edit-group-taxonomique').val() : "rien",
                    famille: ($('#edit-familles').val() !== "") ? $('#edit-familles').val() : "rien",
                    espece: ($('#edit-especes').val() !== "") ? $('#edit-especes').val() : "rien",
                    trait: ($('#edit-traits').val() !== "") ? $('#edit-traits').val() : "rien",
                    modalite: ($('#edit-modalites').val() !== "") ? $('#edit-modalites').val() : "rien",
                };

                var path = {
                    modalite: selectDom.baseUrl() + "/" + selectors.taxonomie + '/' + selectors.famille + '/' + selectors.espece + '/' + selectors.trait,
                    source: selectDom.baseUrl() + "/" + selectors.taxonomie + '/' + selectors.famille + '/' + selectors.espece + '/' + selectors.trait + '/' + selectors.modalite
                };

                // modalite
                $("#edit-modalites").empty(); //To reset traits
                var edit_modalite = $('#edit-modalites');
                edit_modalite.html("<option value selected='selected'>- select -</option>");
                $.getJSON(path.modalite, function(data) {
                    $.each(data, function(i, item) {
                        var option = new Option(item, item);
                        edit_modalite.append($(option));
                    });
                });

                // sources
                $("#edit-sources").empty(); //To reset traits
                var edit_source = $('#edit-sources');
                edit_source.html("<option value selected='selected'>- select -</option>");
                $.getJSON(path.source, function(data) {
                    $.each(data, function(i, item) {
                        var option = new Option(item, item);
                        edit_source.append($(option));
                    });
                });
                e.preventDefault();
            });

            /**
             *  Event Change Modalite
             */
            selectDom.modalite.on("change", function(e) {
                var selectors = {
                    taxonomie: ($('#edit-group-taxonomique').val() !== "") ? $('#edit-group-taxonomique').val() : "rien",
                    famille: ($('#edit-familles').val() !== "") ? $('#edit-familles').val() : "rien",
                    espece: ($('#edit-especes').val() !== "") ? $('#edit-especes').val() : "rien",
                    trait: ($('#edit-traits').val() !== "") ? $('#edit-traits').val() : "rien",
                    modalite: ($('#edit-modalites').val() !== "") ? $('#edit-modalites').val() : "rien",
                };

                var path = {
                    source: selectDom.baseUrl() + "/" + selectors.taxonomie + '/' + selectors.famille + '/' + selectors.espece + '/' + selectors.trait + '/' + selectors.modalite
                };

                // sources
                $("#edit-sources").empty(); //To reset traits
                var edit_source = $('#edit-sources');
                edit_source.html("<option value selected='selected'>- select -</option>");
                $.getJSON(path.source, function(data) {
                    $.each(data, function(i, item) {
                        var option = new Option(item, item);
                        edit_source.append($(option));
                    });
                });
                e.preventDefault();
            });
        },
        detach: function(context, settings, trigger) {
            $('#edit-familles').trigger("keyup");
            $('#edit-especes').trigger("keyup");
            $('#edit-traits').trigger("click");
        }
    };
})(jQuery, Drupal, drupalSettings);