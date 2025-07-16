     <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
         integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
         crossorigin=""/>
     <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
         integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
         crossorigin=""></script>
      <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
      <!-- Leaflet Routing Machine for pathfinding -->
      <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
      <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

      <!-- Add viewport meta tag for mobile rotation support -->
      <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">

      <!-- Audio elements for sound effects -->
      <audio id="emergencySound" preload="none">
          <source src="../assets/sounds/emergency.mp3" type="audio/mpeg">
          <source src="../assets/sounds/emergency.wav" type="audio/wav">
      </audio>

     <style>
         @keyframes emergencyPulse {
             0%, 100% { box-shadow: 0 4px 15px rgba(0,194,100,0.4); }
             50% { box-shadow: 0 6px 25px rgba(0,194,100,0.8); }
         }

         @keyframes messageAlert {
             0%, 100% { transform: scale(1); }
             50% { transform: scale(1.02); }
         }

         @keyframes hazardPulse {
             0%, 100% {
                 transform: scale(1);
                 opacity: 0.8;
             }
             50% {
                 transform: scale(1.2);
                 opacity: 1;
             }
         }

         @keyframes modalSlideIn {
             from {
                 transform: translateY(-50px);
                 opacity: 0;
             }
             to {
                 transform: translateY(0);
                 opacity: 1;
             }
         }

         @keyframes slideIn {
             from {
                 transform: translateX(100%);
                 opacity: 0;
             }
             to {
                 transform: translateX(0);
                 opacity: 1;
             }
         }

         @keyframes slideOut {
             from {
                 transform: translateX(0);
                 opacity: 1;
             }
             to {
                 transform: translateX(100%);
                 opacity: 0;
             }
         }

         @keyframes fadeIn {
             from { opacity: 0; }
             to { opacity: 1; }
         }

         @keyframes spin {
             0% { transform: rotate(0deg); }
             100% { transform: rotate(360deg); }
         }

         @keyframes countdownPulse {
             0%, 100% {
                 transform: scale(1);
                 background: linear-gradient(45deg, #ff4444, #cc0000);
             }
             50% {
                 transform: scale(1.1);
                 background: linear-gradient(45deg, #ff6666, #ff4444);
             }
         }

         .grab-theme {
             background: linear-gradient(135deg, #00c264 0%, #00a556 100%);
         }

         .emergency-btn {
             background: linear-gradient(45deg, #00c264, #00a556);
             animation: emergencyPulse 2s infinite;
             border-radius: 50px;
         }

         .emergency-btn:hover {
             transform: scale(1.05);
             box-shadow: 0 8px 25px rgba(0,194,100,0.6);
         }

         .profile-icon {
             cursor: pointer;
             transition: all 0.3s ease;
         }

         .profile-icon:hover {
             transform: scale(1.1);
         }

         .sidebar {
             position: fixed;
             top: 0;
             right: -280px;
             width: 280px;
             height: 100vh;
             background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
             z-index: 9999;
             transition: all 0.3s ease;
             overflow-y: auto;
         }

         .sidebar.active {
             right: 0;
         }

         .sidebar-overlay {
             position: fixed;
             top: 0;
             left: 0;
             width: 100vw;
             height: 100vh;
             background: rgba(0,0,0,0.5);
             z-index: 9998;
             opacity: 0;
             visibility: hidden;
             transition: all 0.3s ease;
         }

         .sidebar-overlay.active {
             opacity: 1;
             visibility: visible;
         }

         .floating-action-btn {
             position: fixed;
             bottom: 80px;
             right: 20px;
             width: 60px;
             height: 60px;
             border-radius: 50%;
             background: linear-gradient(45deg, #ff4444, #cc0000);
             color: white;
             border: none;
             box-shadow: 0 4px 20px rgba(255,68,68,0.3);
             cursor: pointer;
             display: flex;
             align-items: center;
             justify-content: center;
             font-size: 24px;
             z-index: 1000;
             transition: all 0.3s ease;
         }

         .floating-action-btn:hover {
             transform: scale(1.1);
             box-shadow: 0 6px 25px rgba(255,68,68,0.5);
         }

         .floating-action-btn.hidden {
             display: none;
         }

         .floating-find-btn {
             position: fixed;
             bottom: 150px;
             right: 20px;
             width: 60px;
             height: 60px;
             border-radius: 50%;
             background: linear-gradient(45deg, #00c264, #00a556);
             color: white;
             border: none;
             box-shadow: 0 4px 20px rgba(0,194,100,0.3);
             cursor: pointer;
             display: flex;
             align-items: center;
             justify-content: center;
             font-size: 24px;
             z-index: 1000;
             transition: all 0.3s ease;
         }

         .floating-find-btn:hover {
             transform: scale(1.1);
             box-shadow: 0 6px 25px rgba(0,194,100,0.5);
         }

         .floating-find-btn.hidden {
             display: none;
         }

         .floating-chat-btn {
             position: fixed;
             bottom: 150px;
             right: 20px;
             width: 60px;
             height: 60px;
             border-radius: 50%;
             background: linear-gradient(45deg, #2196f3, #1976d2);
             color: white;
             border: none;
             box-shadow: 0 4px 20px rgba(33,150,243,0.3);
             cursor: pointer;
             display: flex;
             align-items: center;
             justify-content: center;
             font-size: 24px;
             z-index: 1000;
             transition: all 0.3s ease;
         }

         .floating-chat-btn:hover {
             transform: scale(1.1);
             box-shadow: 0 6px 25px rgba(33,150,243,0.5);
         }

         .floating-recenter-btn {
             position: fixed;
             bottom: 220px;
             right: 20px;
             width: 60px;
             height: 60px;
             border-radius: 50%;
             background: linear-gradient(45deg, #9c27b0, #673ab7);
             color: white;
             border: none;
             box-shadow: 0 4px 20px rgba(156,39,176,0.3);
             cursor: pointer;
             display: flex;
             align-items: center;
             justify-content: center;
             font-size: 24px;
             z-index: 1000;
             transition: all 0.3s ease;
         }

         .floating-recenter-btn:hover {
             transform: scale(1.1);
             box-shadow: 0 6px 25px rgba(156,39,176,0.5);
         }

         .control-btn.emergency {
             background: linear-gradient(45deg, #00c264, #00a556);
             border-radius: 25px;
         }

         .control-btn.success {
             background: linear-gradient(45deg, #00c264, #00a556);
             border-radius: 25px;
         }

         .control-btn:hover {
             transform: translateY(-2px);
             box-shadow: 0 4px 15px rgba(0,194,100,0.3);
         }

         .control-btn:disabled {
             opacity: 0.6;
             cursor: not-allowed;
             transform: none;
         }

         .message.dispatcher {
             background: rgba(245, 245, 245, 0.9);
             color: #333;
             border-radius: 20px 20px 20px 5px;
             align-self: flex-start;
         }

         .message.user {
             background: linear-gradient(135deg, #00c264, #00a556);
             color: white;
             border-radius: 20px 20px 5px 20px;
             align-self: flex-end;
         }

         .message.emergency {
             background: linear-gradient(135deg, #ff4444, #cc0000);
             color: #ffffff;
             animation: messageAlert 1s ease-in-out;
             border-radius: 20px;
             align-self: center;
         }

         .message.image {
             background: rgba(245, 245, 245, 0.9);
             border-radius: 20px;
             padding: 8px;
         }

         .message.image img {
             max-width: 200px;
             max-height: 200px;
             border-radius: 10px;
             object-fit: cover;
         }

         .notification-panel {
             transform: translateY(-10px);
             opacity: 0;
             visibility: hidden;
             transition: all 0.3s ease;
             background: linear-gradient(135deg, #1a1a1a, #000000);
             border: 1px solid #333;
             border-radius: 15px;
         }

         .notification-panel.show {
             transform: translateY(0);
             opacity: 1;
             visibility: visible;
         }

         .notification-item:hover {
             background: #2a2a2a;
         }

         .confirmation-modal {
             opacity: 0;
             visibility: hidden;
             transition: all 0.3s ease;
         }

         .confirmation-modal.show {
             opacity: 1;
             visibility: visible;
         }

         .confirmation-content {
             animation: modalSlideIn 0.3s ease-out;
             background: linear-gradient(135deg, #1a1a1a, #000000);
             border: 1px solid #333;
             color: #ffffff;
             border-radius: 20px;
         }

         .countdown-modal {
             position: fixed;
             top: 0;
             left: 0;
             width: 100vw;
             height: 100vh;
             background: rgba(0,0,0,0.9);
             z-index: 10002;
             display: none;
             align-items: center;
             justify-content: center;
         }

         .countdown-modal.show {
             display: flex;
         }

         .countdown-content {
             background: linear-gradient(135deg, #1a1a1a, #000000);
             border: 1px solid #333;
             color: #ffffff;
             border-radius: 20px;
             padding: 2rem;
             max-width: 400px;
             width: 90%;
             text-align: center;
             animation: modalSlideIn 0.3s ease-out;
         }

         .countdown-timer {
             font-size: 4rem;
             font-weight: bold;
             color: #ff4444;
             animation: countdownPulse 1s infinite;
             border-radius: 50%;
             width: 120px;
             height: 120px;
             display: flex;
             align-items: center;
             justify-content: center;
             margin: 0 auto 1rem;
         }

         .confirm-btn {
             background: linear-gradient(45deg, #00c264, #00a556);
             border-radius: 25px;
         }

         .confirm-btn:hover {
             transform: scale(1.05);
         }

         .cancel-btn {
             background: linear-gradient(45deg, #333333, #1a1a1a);
             border-radius: 25px;
         }

         .cancel-btn:hover {
             background: linear-gradient(45deg, #555555, #333333);
         }

         .logout-btn {
             background: linear-gradient(45deg, #dc2626, #b91c1c);
             border-radius: 25px;
         }

         .logout-btn:hover {
             background: linear-gradient(45deg, #ef4444, #dc2626);
         }

         .hazard-point {
             animation: hazardPulse 2s infinite;
         }

         .flood-point {
             background: rgba(33, 150, 243, 0.8);
             border: 3px solid #2196f3;
         }

         .priority-high { background: #ff4444; }
         .priority-medium { background: #ffcc00; }
         .priority-low { background: #00c264; }

         .slide-in {
             animation: slideIn 0.3s ease-out;
         }

         .fade-in {
             animation: fadeIn 0.5s ease-out;
         }

         .chat-messages::-webkit-scrollbar {
             width: 4px;
         }

         .chat-messages::-webkit-scrollbar-track {
             background: rgba(0,0,0,0.1);
         }

         .chat-messages::-webkit-scrollbar-thumb {
             background: rgba(0,194,100,0.6);
             border-radius: 2px;
         }

         .chat-messages::-webkit-scrollbar-thumb:hover {
             background: rgba(0,194,100,0.8);
         }

         .notification-list::-webkit-scrollbar {
             width: 4px;
         }

         .notification-list::-webkit-scrollbar-track {
             background: rgba(0,0,0,0.1);
         }

         .notification-list::-webkit-scrollbar-thumb {
             background: rgba(0,194,100,0.6);
             border-radius: 2px;
         }

         .notification-list::-webkit-scrollbar-thumb:hover {
             background: rgba(0,194,100,0.8);
         }

         .main-container {
             transition: all 0.3s ease;
         }

         .status-compact {
             position: fixed;
             bottom: 20px;
             left: 20px;
             background: rgba(0,0,0,0.9);
             backdrop-filter: blur(10px);
             border-radius: 15px;
             padding: 10px 15px;
             color: white;
             z-index: 1000;
             display: none;
         }

         .loading-overlay {
             position: fixed;
             top: 0;
             left: 0;
             width: 100vw;
             height: 100vh;
             background: rgba(0,0,0,0.8);
             z-index: 10000;
             display: none;
             align-items: center;
             justify-content: center;
         }

         .loading-overlay.show {
             display: flex;
         }

         .ambulance-finder-modal {
             position: fixed;
             top: 0;
             left: 0;
             width: 100vw;
             height: 100vh;
             background: rgba(0,0,0,0.8);
             z-index: 10001;
             display: none;
             align-items: center;
             justify-content: center;
         }

         .ambulance-finder-modal.show {
             display: flex;
         }

         .ambulance-finder-content {
             background: linear-gradient(135deg, #1a1a1a, #000000);
             border: 1px solid #333;
             color: #ffffff;
             border-radius: 20px;
             padding: 2rem;
             max-width: 400px;
             width: 90%;
             text-align: center;
             animation: modalSlideIn 0.3s ease-out;
         }

         .nearest-ambulance {
             background: linear-gradient(45deg, #00c264, #00a556);
             border: 3px solid #ffffff;
             box-shadow: 0 0 20px rgba(0,194,100,0.5);
             animation: emergencyPulse 2s infinite;
         }

         /* Custom styling for Leaflet Routing Machine */
         .leaflet-routing-container {
             background: rgba(26, 26, 26, 0.95);
             color: #ffffff;
             border: 1px solid #333;
             border-radius: 10px;
             padding: 10px;
         }

         .leaflet-routing-container h2,
         .leaflet-routing-container h3 {
             color: #00c264;
         }

         .leaflet-routing-alt {
             background: rgba(0, 0, 0, 0.1);
             border-radius: 5px;
             margin: 5px 0;
         }

         .leaflet-routing-alt:hover {
             background: rgba(0, 194, 100, 0.2);
         }

         .leaflet-routing-geocoders {
             display: none;
         }

         .leaflet-routing-container-hide {
             width: 300px;
         }

         @media (max-width: 768px) {
             .notification-panel {
                 width: calc(100vw - 2rem);
                 right: 1rem;
             }
             .confirmation-modal .confirmation-content {
                 margin: 1rem;
                 width: calc(100% - 2rem);
             }
             .floating-action-btn {
                 width: 50px;
                 height: 50px;
                 font-size: 20px;
                 bottom: 80px;
             }
             .floating-find-btn {
                 width: 50px;
                 height: 50px;
                 font-size: 20px;
                 bottom: 140px;
             }
             .floating-chat-btn {
                 width: 50px;
                 height: 50px;
                 font-size: 20px;
                 bottom: 140px;
             }
             .floating-recenter-btn {
                 width: 50px;
                 height: 50px;
                 font-size: 20px;
                 bottom: 200px;
             }
             .leaflet-routing-container {
                 width: calc(100vw - 2rem) !important;
                 max-width: 280px;
             }
         }

         @media (max-width: 640px) {
             .sidebar {
                 width: 280px;
                 right: -280px;
             }
         }
     </style>
