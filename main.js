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
  fetch('initialize.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      loadTables(); // Φόρτωση των πινάκων μετά την επιτυχή ολοκλήρωση του initialize.php
      console.log('Data stored successfully.');
    })
    .catch(error => {
      console.error('There was a problem with the fetch operation: ', error);
    });
}

const loadTables = () => {
  fetch('load_tables.php')
    .then(response => response.text())
    .then(data => {
      document.getElementById('jsonItemsTable').innerHTML = data; // Εισαγωγή HTML περιεχομένου στον πίνακα jsonItemsTable
      document.getElementById('jsonCategoriesTable').innerHTML = data; // Εισαγωγή HTML περιεχομένου στον πίνακα jsonCategoriesTable
    })
    .catch(error => {
      console.error('There was a problem with the fetch operation: ', error);
    });
}
