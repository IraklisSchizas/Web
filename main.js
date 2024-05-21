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