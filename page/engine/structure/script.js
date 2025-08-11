map.on("click", (event) => {
  console.log(event.latlng);
  onMapClick(event);
});

console.log("Loading map...");
setUserLocation();
alert("Map successfully loaded!");
