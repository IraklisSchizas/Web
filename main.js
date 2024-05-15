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

const initialize = async () => {
  try {
      const response = await fetch('initialize.php');
      if (!response.ok) {
          throw new Error('Network response was not ok');
      }
      loadTables();
      console.log('Data stored successfully.');
  } catch (error) {
      console.error('There was a problem with the fetch operation: ', error);
  }
};

const loadTables = async () => {
  try {
      const response = await fetch('load_tables.php');
      if (!response.ok) {
          throw new Error('There was a problem loading the tables: ' + response.statusText);
      }
      const data = await response.json();
      console.log(data);
      displayItems(data.items);
      displayCategories(data.categories);
  } catch (error) {
      console.error(error);
  }
};

const editRow = (id) => {
  console.log(id);
  const row = document.querySelector(`#jsonItemsTable tbody tr[id="${id}"]`);
  const cells = row.querySelectorAll('td');

  cells[1].setAttribute('contenteditable', 'true');
  cells[2].setAttribute('contenteditable', 'true');
  cells[3].setAttribute('contenteditable', 'true');
  cells[4].setAttribute('contenteditable', 'true');

  const saveButton = document.createElement('button');
  saveButton.textContent = 'Save';
  saveButton.addEventListener('click', () => saveChanges(id));
  cells[5].appendChild(saveButton);

  const cancelButton = document.createElement('button');
  cancelButton.textContent = 'Cancel';
  cancelButton.addEventListener('click', () => cancelEdit(id));
  cells[5].appendChild(cancelButton);
};

const saveChanges = (id) => {
  const row = document.querySelector(`#jsonItemsTable tbody tr[id="${id}"]`);
  const cells = row.querySelectorAll('td');

  const newName = cells[1].textContent;
  const newCategory = cells[2].textContent;
  const newDetails = cells[3].textContent;
  const newQuantity = cells[4].textContent;

  fetch(`update_item.php?id=${id}&name=${newName}&category=${newCategory}&details=${newDetails}&quantity=${newQuantity}`)
      .then(response => {
          if (!response.ok) {
              throw new Error('There was a problem saving the data.');
          }
          return response.text();
      })
      .then(data => {
          console.log('Changes saved successfully:', data);
          loadTables();
      })
      .catch(error => {
          console.error('There was a problem saving the data: ', error);
      });
};

const cancelEdit = (id) => {
  loadTables();
};

const displayItems = (items) => {
  const tableBody = document.getElementById('jsonItemsTable');
  tableBody.innerHTML = '';

  items.forEach(item => {
      const row = tableBody.insertRow();
      row.innerHTML = `<td>${item.id}</td><td>${item.name}</td><td>${item.category}</td><td>${item.details}</td><td>${item.quantity}</td><td><button onclick="editRow(${item.id})">Edit</button></td>`;
  });
};

const displayCategories = (categories) => {
  const tableBody = document.getElementById('jsonCategoriesTable');
  tableBody.innerHTML = '';

  categories.forEach(category => {
      const row = tableBody.insertRow();
      row.innerHTML = `<td>${category.id}</td><td>${category.name}</td>`;
  });
};

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

//window.onload = loadTables;