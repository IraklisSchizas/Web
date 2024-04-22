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
  // Καλείται το PHP script χρησιμοποιώντας ένα αίτημα AJAX
  fetch('initialize.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      console.log('Data stored successfully.');
    })
    .catch(error => {
      console.error('There was a problem with the fetch operation: ', error);
    });
}

// Προστέθηκε 22/4
const loadTables = () => {
  // Καλείται το PHP script για να φορτώσει τους πίνακες
  fetch('load_tables.php')
    .then(response => response.text())
    .then(data => {
      // Εισαγωγή HTML περιεχομένου στα στοιχεία των πινάκων
      document.getElementById('jsonItemsTable').innerHTML = data;
      document.getElementById('jsonCategoriesTable').innerHTML = data;
    })
    .catch(error => {
      console.error('There was a problem with the fetch operation: ', error);
    });
}
