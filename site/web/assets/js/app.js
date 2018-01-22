const GOOGLE_MAPS_KEY = 'AIzaSyAFenKZ1t8RZbQBswYPG2B_fs3c0rQSXMQ';
/**
 * Converts degrees to radians
 *
 * @param deg
 * @returns {number}
 */
function degToRad(deg)
{
    return deg * 2 * Math.PI / 360;
}

/**
 * Computes the distance between two gps points in kmhs
 * @param lat1
 * @param long1
 * @param lat2
 * @param long2
 * @returns {number}
 */
function distance(lat1, long1, lat2, long2)
{
    const R = 6371e3; // metres
    const angle1 = degToRad(lat1);
    const angle2 = degToRad(lat2);
    const diff1 = degToRad(lat2-lat1);
    const diff2 = degToRad(long2-long1);

    const a = Math.sin(diff1/2) * Math.sin(diff1/2) +
        Math.cos(angle1) * Math.cos(angle2) *
        Math.sin(diff2/2) * Math.sin(diff2/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return d = R * c / 1000;
}

function fetchLocationFromAddress(address_text)
{
    const address = encodeURIComponent(address_text);
    const url = 'https://maps.googleapis.com/maps/api/geocode/json?address='+address+'&key=' + GOOGLE_MAPS_KEY;
    $.get(url, function(data) {
        if(data.results.length > 0)
            $('input[name="search[coords]"]')
                .val(data.results[0].geometry.location.lat+','+data.results[0].geometry.location.lng);
    });
}
const $inputLocation = $('input[name="search[location]"]');
$inputLocation.on('keyup', function(event) {
    const val = $(this).val();
    if(val.length >= 2 && val.length % 2 === 0)
        fetchLocationFromAddress(val);
});

$inputLocation.on('focusout', function(event) {
    const val = $(this).val();
    fetchLocationFromAddress(val);
});

$('#geoLocateBtn').click(function(event) {
    event.preventDefault();
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            $('input[name="search[coords]"]').val(lat + ',' + lon);
            const url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='+lat+','+lon+'&key=' + GOOGLE_MAPS_KEY;
            $.get(url, function(data) {
                if(data.results.length > 0)
                    $inputLocation.val(data.results[0].formatted_address);
            });
        });
    }
});

$('#appointment_regular_appointment').on('change', function(event) {
   const $checkbox = $(this);
   const $checkBoxGroup = $checkbox.closest('.form-group');
   const checked = $checkbox.prop('checked');
   if(checked) {
       $checkBoxGroup.parent().find('#appointment_frequency,label[for=appointment_frequency]').show();
       $checkBoxGroup.parent().find('#appointment_frequency_type,label[for=appointment_frequency_type]').show();
   } else {
       $checkBoxGroup.parent().find('#appointment_frequency,label[for=appointment_frequency]').hide();
       $checkBoxGroup.parent().find('#appointment_frequency_type,label[for=appointment_frequency_type]').hide();
   }
});