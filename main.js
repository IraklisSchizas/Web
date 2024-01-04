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
    // Fetch the JSON file using file_get_contents
    fetch('export.json')
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        // Here we handle the data.
        //console.log('Retrieved data:', data);
        // Create a table
        var table = document.createElement("table");
  
        // Create a header row
        var headerRow = table.insertRow();
        for (var key in data.items[0]) {
          var headerCell = headerRow.insertCell();
          headerCell.textContent = key;
        }
  
        // Recursive function to create rows for nested structures
        const createRows = (obj, row) => {
          for (var key in obj) {
            if (key === 'details' && Array.isArray(obj[key]) && obj[key].length > 0) {
              // If the key is 'details' and the value is an array, create a nested table
              var detailsTable = document.createElement("table");
              for (var i = 0; i < obj[key].length; i++) {
                var detailsRow = detailsTable.insertRow();
                var detailNameCell = detailsRow.insertCell();
                var detailValueCell = detailsRow.insertCell();
                detailNameCell.textContent = obj[key][i].detail_name;
                detailValueCell.textContent = obj[key][i].detail_value;
              }
              var cell = row.insertCell();
              cell.appendChild(detailsTable);
            } else if (typeof obj[key] === 'object' && obj[key] !== null) {
              // If the value is an object (excluding 'details'), create a new row
              var newRow = table.insertRow();
              createRows(obj[key], newRow);
            } else {
              // Otherwise, insert a cell with the value
              var cell = row.insertCell();
              cell.textContent = obj[key];
            }
          }
        };
  
        // Create rows for each object in the JSON array
        for (var i = 0; i < data.items.length; i++) {
          var newRow = table.insertRow();
          createRows(data.items[i], newRow);
        }
  
        // Append the table to the container
        jsonItemsTable.appendChild(table);
      
        /* We do the same for the Categories */
        var table2 = document.createElement("table");
        // Create a header row
        var headerRow = table2.insertRow();
        for (var key in data.categories[0]) {
          var headerCell = headerRow.insertCell();
          headerCell.textContent = key;
        }

        // Create rows for each object in the JSON array
        for (var i = 0; i < data.categories.length; i++) {
            var newRow = table2.insertRow();
            createRows(data.categories[i], newRow);
          }
    
          // Append the table to the container
          jsonCategoriesTable.appendChild(table2);
      })
      .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
      });
  }