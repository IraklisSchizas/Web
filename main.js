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
    row.innerHTML = `<td>${item.id}</td><td>${item.name}</td><td>${item.category}</td><td>${item.details}</td><td>${item.quantity}</td><td>${item.edit_button}</td>`;
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

const editRow = (id) => {
  const editForm = document.createElement('form');
  editForm.innerHTML = `
    <label for="editName_${id}">Name:</label><br>
    <input type="text" id="editName_${id}" name="editName_${id}" value=""><br>
    <label for="editCategory_${id}">Category:</label><br>
    <input type="text" id="editCategory_${id}" name="editCategory_${id}" value=""><br>
    <label for="editDetails_${id}">Details:</label><br>
    <input type="text" id="editDetails_${id}" name="editDetails_${id}" value=""><br>
    <label for="editQuantity_${id}">Quantity:</label><br>
    <input type="text" id="editQuantity_${id}" name="editQuantity_${id}" value=""><br>
    <button onclick="saveChanges(${id})">Save</button>
    <button onclick="cancelEdit(${id})">Cancel</button>
  `;
  
  // Εύρεση της γραμμής και αντικατάσταση του περιεχομένου της με τη φόρμα επεξεργασίας
  const row = document.querySelector(`#jsonItemsTable tr[id="${id}"]`);
  row.innerHTML = '';
  row.appendChild(editForm);
}


const saveChanges = (id) => {
  const newName = document.getElementById(`editName_${id}`).value;
  const newCategory = document.getElementById(`editCategory_${id}`).value;
  const newDetails = document.getElementById(`editDetails_${id}`).value;
  const newQuantity = document.getElementById(`editQuantity_${id}`).value;

  // Make a fetch request to update the data in the database
  fetch(`update_item.php?id=${id}&name=${newName}&category=${newCategory}&details=${newDetails}&quantity=${newQuantity}`)
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.text();
    })
    .then(data => {
      // Assuming the response contains a success message or something similar
      console.log('Changes saved successfully:', data);
      // Reload the tables to reflect the changes
      loadTables();
    })
    .catch(error => {
      console.error('There was a problem with the fetch operation: ', error);
    });
}

const cancelEdit = (id) => {
  // Reload the tables without saving changes
  loadTables();
}

// Καλούμε τη συνάρτηση loadTables για να φορτώσουμε τους πίνακες κατά τη φόρτωση της σελίδας
window.onload = loadTables;
