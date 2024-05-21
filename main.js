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