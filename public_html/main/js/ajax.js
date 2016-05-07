/**
 * Fonction d'appel ajax
 */

var O;
if (!O)
    O = {};
else if (typeof O !== "object")
    throw new Error("O already exists & is not 1 object");
if (O.Ajax)
    throw new Error("O.Dialog already exists");

var lst_pres = [];
var lst_fac = [];

O.Ajax = {
    addService: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        }).done(function () {
            setTimeout(function () {
                location.reload();
            }, 500);
        });
    },
    editService: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        }).done(function () {
            setTimeout(function () {
                location.reload();
            }, 500);
        });
    },
    delService: function (data) {
        var _data = {
            mod: 'delService',
            svc_num: data
        };
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: _data,
            cache: false
        }).done(function () {
            setTimeout(function () {
                location.reload();
            }, 500);
        });
    },
    getService: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            dataType: 'json',
            cache: false
        }).done(function (response) {
            var res = response;
            $('#js_get_service .name').html($('#cli_nom').val());
            $('#js_get_service .pname').html($('#cli_prenom').val());
            $('#js_get_service .tech').html(res['fam_libelle']);
            $('#js_get_service .date').html(res['svc_date']);
            $('#js_get_service #details').html(res['svc_details']);
        });
    },
    addDepense: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        }).done(function (response) {
            $('.return').html(response);
            setTimeout(function () {
                location.reload();
            }, 500);
        });
    },
    addClient: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        }).done(function () {
            setTimeout(function () {
                location.reload();
            }, 500);
        });
    },
    editClient: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        }).done(function () {
            setTimeout(function () {
                location.reload();
            }, 500);
        });
    },
    delClient: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        }).done(function () {
            setTimeout(function () {
                location.href = 'index.php';
            }, 500);
        });
    },
    getPres: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            dataType: 'json',
            cache: false
        }).done(function (response) {
            $.each(response, function (key, val) {
                $('#prestations').append('<tr data-pres-num="' + val['pres_num'] + '"><td>' + val['pres_libelle'] + '</td><td class="price" data-price="' + val['pres_prix'] + '"><input type="text" value="' + val['pres_prix_build'] + '" disabled="disabled"></td></tr>');
            });
            $('#prestations tr').on('click', function () {
                $(this).toggleClass('selected');
                var index = lst_pres.indexOf($(this).data('pres-num'));
                if (index > -1) {
                    lst_pres.splice(index);
                } else {
                    lst_pres.push($(this).data('pres-num'));
                }
            });
        });
    },
    addPrestation: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        }).done(function () {
            setTimeout(function () {
                location.reload();
            }, 500);
        });
    },
    delPresFac: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        }).done(function () {
            setTimeout(function () {
                location.reload();
            }, 500);
        });
    },
    validateFac: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        }).done(function () {
            setTimeout(function () {
                location.reload();
            }, 500);
        });
    },
    delFac: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        }).done(function () {
            setTimeout(function () {
                window.location.href = 'index.php';
            }, 500);
        });
    },
    addPai: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        });
    },
    getWaitingFac: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
			dataType: 'json',
            cache: false
        }).done(function (response) {
            $.each(response, function (key, val) {
                $('#factures').append('<tr data-fac-num="' + val['fac_num'] + '"><td>' + val['fac_num'] + '</td><td>' + val['cli_nom'] + '</td><td>' + val['cli_prenom'] + '</td></tr>');
            });
			$('#factures tr').on('click', function () {
                $(this).toggleClass('selected');
                var index = lst_fac.indexOf($(this).data('fac-num'));
                if (index > -1) {
                    lst_fac.splice(index);
                } else {
                    lst_fac.push($(this).data('fac-num'));
                }
            });
        });
    },
    addWaitingFac: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        }).done(function () {
			var sessionx = {};
            setTimeout(function () {
                location.reload();
            }, 500);
        });
    },
    delIncludedFac: function (data) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        }).done(function () {
            setTimeout(function () {
                location.reload();
            }, 500);
        });
    },
	sessionSetter: function (data) {
		$.ajax({
            method: "POST",
            url: "ajax.php",
            data: data,
            cache: false
        });
	}
};