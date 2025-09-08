import { useEffect, useRef } from 'react';
import { Map as LeafletMap } from 'leaflet';
import * as L from 'leaflet';

interface MapComponentProps {
  center?: [number, number];
  zoom?: number;
  onLocationUpdate?: (lat: number, lng: number) => void;
}

const MapComponent: React.FC<MapComponentProps> = ({
  center = [14.5995, 120.9842], // Manila, Philippines default
  zoom = 13,
  onLocationUpdate
}) => {
  const mapRef = useRef<HTMLDivElement>(null);
  const mapInstanceRef = useRef<LeafletMap | null>(null);
  const userMarkerRef = useRef<L.Marker | null>(null);

  useEffect(() => {
    if (!mapRef.current) return;

    // Fix for Leaflet default markers
    delete (L.Icon.Default.prototype as any)._getIconUrl;
    L.Icon.Default.mergeOptions({
      iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
      iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    });

    // Initialize map
    const map = L.map(mapRef.current).setView(center, zoom);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    mapInstanceRef.current = map;

    // Get user's current location
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          const userPos: [number, number] = [lat, lng];
          
          // Center map on user location
          map.setView(userPos, 15);
          
          // Create custom user location marker
          const userIcon = L.divIcon({
            className: 'user-location-marker',
            html: `
              <div class="user-marker">
                <div class="user-marker-inner"></div>
                <div class="user-marker-pulse"></div>
              </div>
            `,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
          });
          
          // Add user location marker
          userMarkerRef.current = L.marker(userPos, { icon: userIcon })
            .addTo(map)
            .bindPopup('Your Location')
            .openPopup();
          
          // Notify parent component
          onLocationUpdate?.(lat, lng);
        },
        (error) => {
          console.error('Error getting user location:', error);
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 60000
        }
      );
    }

    return () => {
      map.remove();
    };
  }, []);

  return (
    <div className="relative w-full h-full">
      <div ref={mapRef} className="w-full h-full" />
      
      {/* Custom CSS for user location marker */}
      <style>{`
        .user-marker {
          position: relative;
          width: 20px;
          height: 20px;
        }
        
        .user-marker-inner {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          width: 12px;
          height: 12px;
          background-color: #3b82f6;
          border: 2px solid white;
          border-radius: 50%;
          box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .user-marker-pulse {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          width: 20px;
          height: 20px;
          background-color: #3b82f6;
          border-radius: 50%;
          opacity: 0.3;
          animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
          0% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 0.3;
          }
          100% {
            transform: translate(-50%, -50%) scale(2);
            opacity: 0;
          }
        }
        
        .leaflet-routing-alternatives-container {
          display: none !important;
        }
        
        .leaflet-routing-container {
          display: none !important;
        }
      `}</style>
    </div>
  );
};

export default MapComponent;