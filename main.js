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

// Φορτώνει τους πίνακες με τα δεδομένα από τη βάση δεδομένων
function loadTables() {
  fetch('load_tables.php')
      .then(response => response.json())
      .then(data => {
          const itemsTableBody = document.querySelector('#jsonItemsTable tbody');
          const categoriesTable = document.querySelector('#jsonCategoriesTable');

          // Φορτώνει τον πίνακα με τα αντικείμενα
          itemsTableBody.innerHTML = '';
          data.items.forEach(item => {
              const row = document.createElement('tr');
              row.innerHTML = `
                  <td>${item.id}</td>
                  <td>${item.name}</td>
                  <td>${item.category}</td>
                  <td>${item.details}</td>
                  <td>${item.quantity}</td>
                  <td><button onclick="editRow(${item.id})">Επεξεργασία</button></td>
              `;
              itemsTableBody.appendChild(row);
          });

          // Φορτώνει τον πίνακα με τις κατηγορίες
          categoriesTable.innerHTML = '';
          data.categories.forEach(category => {
              const row = document.createElement('tr');
              row.innerHTML = `
                  <td>${category.id}</td>
                  <td>${category.name}</td>
              `;
              categoriesTable.appendChild(row);
          });
      })
      .catch(error => console.error('Υπήρξε πρόβλημα με τη φόρτωση των πινάκων: ', error));
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

// Εμφανίζει τη φόρμα επεξεργασίας για το επιλεγμένο αντικείμενο
function editRow(id) {
  console.log(id);
  const cell = document.querySelector(`#jsonItemsTable tbody tr td:first-child`).textContent;
  const row = cell.parentNode;
  const cells = row.querySelectorAll('td');

  document.getElementById('editItemId').value = cells[0].textContent;
  document.getElementById('editItemName').value = cells[1].textContent;
  document.getElementById('editItemCategory').value = cells[2].textContent;
  document.getElementById('editItemDetails').value = cells[3].textContent;
  document.getElementById('editItemQuantity').value = cells[4].textContent;

  document.getElementById('editForm').style.display = 'block';
}

// Ακυρώνει τη φόρμα επεξεργασίας και επαναφέρει τα αρχικά δεδομένα
document.getElementById('cancelEditButton').addEventListener('click', function() {
  document.getElementById('editForm').style.display = 'none';
});

// Αποθηκεύει τις αλλαγές που έγιναν στη φόρμα επεξεργασίας
document.getElementById('saveEditButton').addEventListener('click', function() {
  const id = document.getElementById('editItemId').value;
  const name = document.getElementById('editItemName').value;
  const category = document.getElementById('editItemCategory').value;
  const details = document.getElementById('editItemDetails').value;
  const quantity = document.getElementById('editItemQuantity').value;

  fetch('update_item.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json'
      },
      body: JSON.stringify({
          id: id,
          name: name,
          category: category,
          details: details,
          quantity: quantity
      })
  })
  .then(response => {
      if (!response.ok) {
          throw new Error('Υπήρξε πρόβλημα κατά την αποθήκευση των δεδομένων.');
      }
      return response.json();
  })
  .then(data => {
      console.log('Τα δεδομένα αποθηκεύτηκαν με επιτυχία: ', data);
      document.getElementById('editForm').style.display = 'none';
      loadTables(); // Φορτώνει εκ νέου τους πίνακες με τα δεδομένα
  })
  .catch(error => console.error('Υπήρξε πρόβλημα κατά την αποθήκευση των δεδομένων: ', error));
});

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

function getCurrentDateTime() {
  // Δημιουργία ενός αντικειμένου Date που αντιστοιχεί στην τρέχουσα ημερομηνία και ώρα
  let currentDate = new Date();

  // Λήψη της ημερομηνίας
  let year = currentDate.getFullYear();
  let month = ('0' + (currentDate.getMonth() + 1)).slice(-2); // προσθέτει τυχόν πρόσθετο "0" στους μήνες από 1 έως 9
  let day = ('0' + currentDate.getDate()).slice(-2); // προσθέτει τυχόν πρόσθετο "0" στις ημέρες από 1 έως 9

  // Λήψη της ώρας
  let hours = ('0' + currentDate.getHours()).slice(-2); // προσθέτει τυχόν πρόσθετο "0" στις ώρες από 0 έως 9
  let minutes = ('0' + currentDate.getMinutes()).slice(-2); // προσθέτει τυχόν πρόσθετο "0" στα λεπτά από 0 έως 9
  let seconds = ('0' + currentDate.getSeconds()).slice(-2); // προσθέτει τυχόν πρόσθετο "0" στα δευτερόλεπτα από 0 έως 9

  // Επιστροφή της ημερομηνίας και ώρας σε μορφή κειμένου
  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

// Καλούμε τη συνάρτηση loadTables για να φορτώσουμε τους πίνακες κατά τη φόρτωση της σελίδας
window.onload = loadTables;
