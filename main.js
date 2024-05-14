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
    .then(response => response.json())
    .then(data => {
      // Εισάγουμε τα δεδομένα στους πίνακες χρησιμοποιώντας το JSON
      populateItemsTable(data.items);
      populateCategoriesTable(data.categories);
    })
    .catch(error => {
      console.error('Υπήρξε πρόβλημα με τη φόρτωση των πινάκων: ', error);
    });
}

const populateItemsTable = (items) => {
  const tableBody = document.getElementById('jsonItemsTable');
  // Καθαρίζουμε τον πίνακα πριν την εισαγωγή των νέων δεδομένων
  tableBody.innerHTML = '';

  items.forEach(item => {
    const row = tableBody.insertRow();
    row.innerHTML = `<td>${item.id}</td><td>${item.name}</td><td>${item.category}</td><td>${item.details}</td>`;
  });
}

const populateCategoriesTable = (categories) => {
  const tableBody = document.getElementById('jsonCategoriesTable');
  // Καθαρίζουμε τον πίνακα πριν την εισαγωγή των νέων δεδομένων
  tableBody.innerHTML = '';

  categories.forEach(category => {
    const row = tableBody.insertRow();
    row.innerHTML = `<td>${category.id}</td><td>${category.name}</td>`;
  });
}

// Καλούμε τη συνάρτηση loadTables για να φορτώσουμε τους πίνακες κατά τη φόρτωση της σελίδας
window.onload = loadTables;
