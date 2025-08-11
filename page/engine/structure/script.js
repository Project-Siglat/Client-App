map.on("click", (event) => {
  console.log(event.latlng);
  onMapClick(event);
});

setUserLocation();
