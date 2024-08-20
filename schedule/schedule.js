$(function() {
    // Initialiser le datepicker pour la sélection de la date
    $("#datepicker").datepicker({
        beforeShowDay: function(date) {
            // Désactive les dimanches (0) et les lundis (1)
            var day = date.getDay();
            return [(day != 0 && day != 1)];
        },
        minDate: new Date(), // Limite la sélection à partir de la date actuelle
        dateFormat: "yy-mm-dd", // Format de la date pour la compatibilité avec MySQL
        onSelect: function(dateText, inst) {
            var selectedDate = $(this).datepicker('getDate');
            var dayOfWeek = selectedDate.getDay();
            $('#time').timepicker('remove'); // Enlève le timepicker précédent pour éviter les instances multiples
            // Configure le timepicker en fonction du jour de la semaine
            if (dayOfWeek == 6) { // Samedi
                $('#time').timepicker({
                    'timeFormat': 'H:i',
                    'minTime': '09:00',
                    'maxTime': '19:00',
                    'step': 30,
                    'disableTextInput': true
                });
            } else {
                $('#time').timepicker({
                    'timeFormat': 'H:i',
                    'minTime': '09:30',
                    'maxTime': '18:30',
                    'step': 30,
                    'disableTextInput': true
                });
            }
            $('#time').prop('disabled', false); // Active le champ du temps
        }
    });

    // Création d'un déclencheur personnalisé pour le calendrier
    $("#calendar-icon").click(function() {
        $("#datepicker").datepicker("show");
    });

    // Écouteur d'événements pour la soumission du formulaire
    $('#appointmentForm').on('submit', function(event) {
        event.preventDefault(); // Empêche la soumission standard du formulaire

        var name = $("#name").val();
        var date = $("#datepicker").val();
        var time = $("#time").val();
        var barberId = $("#barber_id").val();
        var serviceId = $("#service_id").val();

        // Appel AJAX pour enregistrer l'appointment
        $.ajax({
            url: '../bookAppointment.php',
            type: 'POST',
            data: {
                name: $("#name").val(),
                date: $("#datepicker").val(),
                time: $("#time").val(),
                barber_id: $("#barber_id").val(),
                service_id: $("#service_id").val()
            },
            success: function(response) {
                alert("Rendez-vous programmé avec succès !");
                // redirection ou autre logique ici
            },
            error: function(xhr, status, error) {
                alert("Erreur lors de la programmation du rendez-vous : " + error);
            }
        });
    });
});
