    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) {
            console.error('Calendar element not found!');
            return;
        }

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: new Date(), // Automatically set to today's date
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                console.log('Fetching events from:', '/eduspace-html/holidays.php');
                fetch('/eduspace-html/holidays.php')
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Fetched events data:', data);
                        if (data.status === 'success') {
                            if (data.events.length === 0) {
                                console.warn('No events found in response');
                            }
                            const eventsWithColors = data.events.map(event => {
                                let color;
                                switch (event.category) {
                                    case 'uni_holidays':
                                        color = '#ff9800';
                                        break;
                                    case 'public_holidays':
                                        color = '#d4af37';
                                        break;
                                    case 'final_exam':
                                        color = '#f44336';
                                        break;
                                    default:
                                        color = '#d4af37';
                                }
                                return { ...event, color };
                            });
                            console.log('Processing events:', eventsWithColors);
                            successCallback(eventsWithColors);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                                footer: '<a href="#" style="color: #8b0000;">Close</a>',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#8b0000'
                            });
                            failureCallback(new Error(data.message));
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Unexpected Error',
                            text: error.message,
                            footer: '<a href="#" style="color: #8b0000;">Close</a>',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#8b0000'
                        });
                        failureCallback(error);
                    });
            },
            eventClick: function(info) {
                console.log('Event clicked:', info.event);
                Swal.fire({
                    icon: 'info',
                    title: info.event.title,
                    text: `Start: ${info.event.start.toLocaleDateString()}${info.event.end ? ', End: ' + info.event.end.toLocaleDateString() : ''}, Category: ${info.event.extendedProps.category}`,
                    footer: '<a href="#" style="color: #d4af37;">Close</a>',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d4af37'
                });
            },
            eventDidMount: function(info) {
                console.log('Event rendered:', info.event.title, info.event.start, info.event.end, info.event.extendedProps.category);
            }
        });

        calendar.render();
        console.log('Calendar rendered');
    });