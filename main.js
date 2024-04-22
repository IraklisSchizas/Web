const getLocation = () => {
    
    const success = (position) => {
        document.getElementById('latitude').value = position.coords.latitude;
        document.getElementById('longitude').value = position.coords.longitude;
    }

    const error = () => {
        window.alert('Unable to access location!');
    }

    navigator.geolocation.getCurrentPosition(success, error);
}

const initialize = () => {
  // Καλούμε το PHP script με ένα αίτημα AJAX
  fetch('initialize.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      // Εκτελούμε τη συνάρτηση loadTables() μετά την επιτυχή ολοκλήρωση του initialize.php
      loadTables();
      console.log('Data stored successfully.');
    })
    .catch(error => {
      console.error('There was a problem with the fetch operation: ', error);
    });
}

const loadTables = () => {
  // Καλούμε το PHP script για να φορτώσει τους πίνακες
  fetch('load_tables.php')
    .then(response => response.text())
    .then(data => {
      // Εισάγουμε το HTML περιεχόμενο στα στοιχεία των πινάκων
      document.getElementById('jsonItemsTable').innerHTML = data;
      document.getElementById('jsonCategoriesTable').innerHTML = data;
    })
    .catch(error => {
      console.error('There was a problem with the fetch operation: ', error);
    });
}

// Καλούμε τη συνάρτηση initialize() όταν φορτώνει η σελίδα
//window.onload = () => {
//  document.getElementById('j_button').addEventListener('click', initialize);
//};
