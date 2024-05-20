const getLocation = () => {
  const success = (position) => {
      document.getElementById('latitude').value = position.coords.latitude;
      document.getElementById('longitude').value = position.coords.longitude;
  };

  const error = () => {
      window.alert('Unable to access location!');
  };

  navigator.geolocation.getCurrentPosition(success, error);
};

const initialize = () => {
  fetch('initialize.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      loadTables();
      console.log('Data stored successfully.');
    })
    .catch(error => {
      console.error('There was a problem with the fetch operation: ', error);
    });
}

/*async function loadTables() {
  try {
    const response = await fetch('load_tables.php');
    if (!response.ok) {
      throw new Error('There was a problem loading the tables: ' + response.statusText);
    }
    const data = await response.json();
    console.log(data);

    populateItemsTable(data);
    populateCategoriesTable(data);
  } catch (error) {
    console.error(error);
  }
}

function editRow(id) {
  const row = document.getElementById(`item_${id}`);
  const cells = row.querySelectorAll('td');

  document.getElementById('editItemId').value = cells[0].textContent;
  document.getElementById('editItemName').value = cells[1].textContent;
  document.getElementById('editItemCategory').value = cells[2].textContent;
  document.getElementById('editItemDetails').value = cells[3].textContent;
  document.getElementById('editItemQuantity').value = cells[4].textContent;

  document.getElementById('editForm').style.display = 'block';
}*/

/*document.getElementById('cancelEditButton').addEventListener('click', function() {
  document.getElementById('editForm').style.display = 'none';
});

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
          throw new Error('There was a problem saving the data.');
      }
      return response.json();
  })
  .then(data => {
      console.log('Data saved successfully: ', data);
      document.getElementById('editForm').style.display = 'none';
      loadTables();
  })
  .catch(error => console.error('There was a problem saving the data: ', error));
});*/

/*function populateItemsTable(items) {
  const tableBody = document.getElementById('jsonItemsTable');
  tableBody.innerHTML = '';

  items.forEach(item => {
    const row = tableBody.insertRow();
    row.id = `item_${item.id}`;
    row.innerHTML = `<td>${item.id}</td><td>${item.name}</td><td>${item.category}</td><td>${item.details}</td><td>${item.quantity}</td><td><button onclick="editRow(${item.id})">Edit</button></td>`;
  });
}

function populateCategoriesTable(categories) {
  const tableBody = document.getElementById('jsonCategoriesTable');
  tableBody.innerHTML = '';

  categories.forEach(category => {
    const row = tableBody.insertRow();
    row.innerHTML = `<td>${category.id}</td><td>${category.name}</td>`;
  });
};*/

const getCurrentDateTime = () => {
  const currentDate = new Date();
  const year = currentDate.getFullYear();
  const month = ('0' + (currentDate.getMonth() + 1)).slice(-2);
  const day = ('0' + currentDate.getDate()).slice(-2);
  const hours = ('0' + currentDate.getHours()).slice(-2);
  const minutes = ('0' + currentDate.getMinutes()).slice(-2);
  const seconds = ('0' + currentDate.getSeconds()).slice(-2);
  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
};