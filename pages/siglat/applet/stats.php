<div class="w-full h-full rounded-lg shadow-lg p-6" style="background-color: #2E3440;">
  <div class="mb-6 flex justify-between items-end">
    <div>
      <label for="date-selector" class="block text-sm font-medium mb-2" style="color: #D8DEE9;">📅 Select Date</label>
      <input type="date" id="date-selector" class="rounded-md px-3 py-2 focus:outline-none focus:ring-2" style="border: 1px solid #4C566A; background-color: #3B4252; color: #ECEFF4; --tw-ring-color: #5E81AC;">
    </div>
    <button id="print-button" class="px-4 py-2 rounded-md font-medium transition-colors" style="background-color: #5E81AC; color: #ECEFF4; border: 1px solid #81A1C1;" onmouseover="this.style.backgroundColor='#81A1C1'" onmouseout="this.style.backgroundColor='#5E81AC'">
      🖨️ Print Report
    </button>
  </div>

  <div class="mb-8 rounded-lg p-6" style="background-color: #3B4252; border: 1px solid #4C566A;">
    <h2 class="text-xl font-semibold mb-4" style="color: #D8DEE9;">📈 Incident Distribution</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Bar Chart -->
      <div>
        <h3 class="text-lg font-medium mb-4" style="color: #D8DEE9;">Incident Counts</h3>
        <div class="space-y-4" id="incident-bars">
          <div>
            <div class="flex justify-between mb-1">
              <span style="color: #BF616A;">🚗 Vehicular</span>
              <span style="color: #BF616A;" id="vehicular-count">0</span>
            </div>
            <div class="w-full rounded-full h-3" style="background-color: #4C566A;">
              <div class="h-3 rounded-full" style="width: 0%; background-color: #BF616A;" id="vehicular-bar"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between mb-1">
              <span style="color: #5E81AC;">⚕️ Medical</span>
              <span style="color: #5E81AC;" id="medical-count">0</span>
            </div>
            <div class="w-full rounded-full h-3" style="background-color: #4C566A;">
              <div class="h-3 rounded-full" style="width: 0%; background-color: #5E81AC;" id="medical-bar"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between mb-1">
              <span style="color: #D08770;">🔥 Fire</span>
              <span style="color: #D08770;" id="fire-count">0</span>
            </div>
            <div class="w-full rounded-full h-3" style="background-color: #4C566A;">
              <div class="h-3 rounded-full" style="width: 0%; background-color: #D08770;" id="fire-bar"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pie Chart Representation -->
      <div>
        <h3 class="text-lg font-medium mb-4" style="color: #D8DEE9;">Distribution Overview</h3>
        <div class="flex items-center justify-center">
          <div class="relative w-40 h-40">
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 42 42" id="pie-chart">
              <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="#BF616A" stroke-width="3" stroke-dasharray="0 100" stroke-dashoffset="0" id="vehicular-pie"></circle>
              <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="#5E81AC" stroke-width="3" stroke-dasharray="0 100" stroke-dashoffset="0" id="medical-pie"></circle>
              <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="#D08770" stroke-width="3" stroke-dasharray="0 100" stroke-dashoffset="0" id="fire-pie"></circle>
            </svg>
          </div>
        </div>
        <div class="mt-4 space-y-2" id="pie-legend">
          <div class="flex items-center">
            <div class="w-3 h-3 rounded-full mr-2" style="background-color: #BF616A;"></div>
            <span class="text-sm" style="color: #D8DEE9;" id="vehicular-legend">Vehicular (0%)</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 rounded-full mr-2" style="background-color: #5E81AC;"></div>
            <span class="text-sm" style="color: #D8DEE9;" id="medical-legend">Medical (0%)</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 rounded-full mr-2" style="background-color: #D08770;"></div>
            <span class="text-sm" style="color: #D8DEE9;" id="fire-legend">Fire (0%)</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <div class="rounded-lg p-4" style="background-color: #3B4252; border: 1px solid #BF616A;">
      <h3 class="text-lg font-semibold mb-2" style="color: #BF616A;">🚗💥 Vehicular Accident</h3>
      <p class="text-3xl font-bold" style="color: #BF616A;" id="total-vehicular">0</p>
    </div>

    <div class="rounded-lg p-4" style="background-color: #3B4252; border: 1px solid #5E81AC;">
      <h3 class="text-lg font-semibold mb-2" style="color: #5E81AC;">⚕️ Medical Emergency</h3>
      <p class="text-3xl font-bold" style="color: #5E81AC;" id="total-medical">0</p>
    </div>

    <div class="rounded-lg p-4" style="background-color: #3B4252; border: 1px solid #A3BE8C;">
      <h3 class="text-lg font-semibold mb-2" style="color: #A3BE8C;">👥 Number of Users</h3>
      <p class="text-3xl font-bold" style="color: #A3BE8C;" id="total-users">0</p>
    </div>

    <div class="rounded-lg p-4" style="background-color: #3B4252; border: 1px solid #EBCB8B;">
      <h3 class="text-lg font-semibold mb-2" style="color: #EBCB8B;">🚑 Number of Ambulance</h3>
      <p class="text-3xl font-bold" style="color: #EBCB8B;" id="total-ambulance">0</p>
    </div>

    <div class="rounded-lg p-4" style="background-color: #3B4252; border: 1px solid #B48EAD;">
      <h3 class="text-lg font-semibold mb-2" style="color: #B48EAD;">📊 Total Response</h3>
      <p class="text-3xl font-bold" style="color: #B48EAD;" id="total-response">0</p>
    </div>

    <div class="rounded-lg p-4" style="background-color: #3B4252; border: 1px solid #D08770;">
      <h3 class="text-lg font-semibold mb-2" style="color: #D08770;">🔥 Fire Incident</h3>
      <p class="text-3xl font-bold" style="color: #D08770;" id="total-fire">0</p>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
  <script>
    let allAmbulanceData = [];
    let monthlyData = {};

    document.addEventListener('DOMContentLoaded', function() {
      const dateSelector = document.getElementById('date-selector');
      const printButton = document.getElementById('print-button');
      const today = new Date();
      const formattedDate = today.getFullYear() + '-' +
        String(today.getMonth() + 1).padStart(2, '0') + '-' +
        String(today.getDate()).padStart(2, '0');
      dateSelector.value = formattedDate;

      // Load initial data
      loadAmbulanceData();

      // Add event listener for date changes
      dateSelector.addEventListener('change', loadAmbulanceData);

      // Add event listener for print button
      printButton.addEventListener('click', generatePDFReport);
    });

    async function loadAmbulanceData() {
      try {
        const response = await fetch(`${API()}/api/v1/Ambulance/all-alert`, {
          method: 'GET',
          headers: {
            'accept': '*/*'
          }
        });

        if (!response.ok) {
          throw new Error('Network response was not ok');
        }

        const data = await response.json();
        allAmbulanceData = data;
        processMonthlyData(data);
        processAmbulanceData(data);
      } catch (error) {
        console.error('Error fetching ambulance data:', error);
      }
    }

    function processMonthlyData(data) {
      monthlyData = {
        vehicular: new Array(12).fill(0),
        medical: new Array(12).fill(0),
        fire: new Array(12).fill(0),
        users: new Array(12).fill(0),
        ambulance: new Array(12).fill(0),
        response: new Array(12).fill(0)
      };

      const monthlyUsers = Array.from({length: 12}, () => new Set());
      const monthlyAmbulances = Array.from({length: 12}, () => new Set());

      data.forEach(item => {
        const date = new Date(item.respondedAt);
        const month = date.getMonth();

        if (item.what === 'accident') {
          monthlyData.vehicular[month]++;
        } else if (item.what === 'medical') {
          monthlyData.medical[month]++;
        } else if (item.what === 'fire') {
          monthlyData.fire[month]++;
        }

        monthlyData.response[month]++;

        if (item.uid) monthlyUsers[month].add(item.uid);
        if (item.responder) monthlyAmbulances[month].add(item.responder);
      });

      for (let i = 0; i < 12; i++) {
        monthlyData.users[i] = monthlyUsers[i].size;
        monthlyData.ambulance[i] = monthlyAmbulances[i].size;
      }
    }

    function processAmbulanceData(data) {
      const selectedDate = document.getElementById('date-selector').value;

      // Filter data by selected date if needed
      let filteredData = data;
      if (selectedDate) {
        filteredData = data.filter(item => {
          const itemDate = new Date(item.respondedAt).toISOString().split('T')[0];
          return itemDate === selectedDate;
        });
      }

      // Count incidents by type
      let vehicularCount = 0;
      let medicalCount = 0;
      let fireCount = 0;
      let totalResponses = filteredData.length;
      let uniqueUsers = new Set();
      let uniqueResponders = new Set();

      filteredData.forEach(item => {
        if (item.what === 'accident') {
          vehicularCount++;
        } else if (item.what === 'medical') {
          medicalCount++;
        } else if (item.what === 'fire') {
          fireCount++;
        }

        if (item.uid) uniqueUsers.add(item.uid);
        if (item.responder) uniqueResponders.add(item.responder);
      });

      const total = vehicularCount + medicalCount + fireCount;

      // Update counts
      document.getElementById('vehicular-count').textContent = vehicularCount;
      document.getElementById('medical-count').textContent = medicalCount;
      document.getElementById('fire-count').textContent = fireCount;

      document.getElementById('total-vehicular').textContent = vehicularCount;
      document.getElementById('total-medical').textContent = medicalCount;
      document.getElementById('total-fire').textContent = fireCount;
      document.getElementById('total-users').textContent = uniqueUsers.size;
      document.getElementById('total-ambulance').textContent = uniqueResponders.size;
      document.getElementById('total-response').textContent = totalResponses;

      // Update bar charts
      if (total > 0) {
        const vehicularPercent = (vehicularCount / total) * 100;
        const medicalPercent = (medicalCount / total) * 100;
        const firePercent = (fireCount / total) * 100;

        document.getElementById('vehicular-bar').style.width = vehicularPercent + '%';
        document.getElementById('medical-bar').style.width = medicalPercent + '%';
        document.getElementById('fire-bar').style.width = firePercent + '%';

        // Update pie chart
        updatePieChart(vehicularPercent, medicalPercent, firePercent);

        // Update legend
        document.getElementById('vehicular-legend').textContent = `Vehicular (${vehicularPercent.toFixed(1)}%)`;
        document.getElementById('medical-legend').textContent = `Medical (${medicalPercent.toFixed(1)}%)`;
        document.getElementById('fire-legend').textContent = `Fire (${firePercent.toFixed(1)}%)`;
      }
    }

    function updatePieChart(vehicularPercent, medicalPercent, firePercent) {
      const vehicularDash = vehicularPercent;
      const medicalDash = medicalPercent;
      const fireDash = firePercent;

      const vehicularPie = document.getElementById('vehicular-pie');
      const medicalPie = document.getElementById('medical-pie');
      const firePie = document.getElementById('fire-pie');

      vehicularPie.setAttribute('stroke-dasharray', `${vehicularDash} 100`);
      vehicularPie.setAttribute('stroke-dashoffset', '0');

      medicalPie.setAttribute('stroke-dasharray', `${medicalDash} 100`);
      medicalPie.setAttribute('stroke-dashoffset', `-${vehicularDash}`);

      firePie.setAttribute('stroke-dasharray', `${fireDash} 100`);
      firePie.setAttribute('stroke-dashoffset', `-${vehicularDash + medicalDash}`);
    }

    function generatePDFReport() {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF({
        orientation: 'landscape',
        unit: 'mm',
        format: 'legal'
      });

      // Title
      doc.setFontSize(20);
      doc.text('Emergency Response Annual Report', 20, 20);

      // Date generated
      doc.setFontSize(12);
      const currentDate = new Date().toLocaleDateString();
      doc.text(`Generated on: ${currentDate}`, 20, 30);

      // Prepare table data
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

      const tableData = [
        ['Vehicular Accidents', ...monthlyData.vehicular, monthlyData.vehicular.reduce((a, b) => a + b, 0)],
        ['Medical Emergencies', ...monthlyData.medical, monthlyData.medical.reduce((a, b) => a + b, 0)],
        ['Fire Incidents', ...monthlyData.fire, monthlyData.fire.reduce((a, b) => a + b, 0)],
        ['Number of Users', ...monthlyData.users, monthlyData.users.reduce((a, b) => a + b, 0)],
        ['Number of Ambulances', ...monthlyData.ambulance, monthlyData.ambulance.reduce((a, b) => a + b, 0)],
        ['Total Responses', ...monthlyData.response, monthlyData.response.reduce((a, b) => a + b, 0)]
      ];

      // Calculate totals row
      const totalsRow = ['Total'];
      for (let i = 0; i < 12; i++) {
        const monthTotal = monthlyData.vehicular[i] + monthlyData.medical[i] + monthlyData.fire[i] +
                          monthlyData.users[i] + monthlyData.ambulance[i] + monthlyData.response[i];
        totalsRow.push(monthTotal);
      }
      const grandTotal = totalsRow.slice(1, 13).reduce((a, b) => a + b, 0);
      totalsRow.push(grandTotal);

      tableData.push(totalsRow);

      // Create table
      doc.autoTable({
        head: [['Category', ...months, 'Total']],
        body: tableData,
        startY: 40,
        styles: { fontSize: 10, cellPadding: 3 },
        headStyles: { fillColor: [94, 129, 172] },
        alternateRowStyles: { fillColor: [240, 240, 240] },
        footStyles: { fillColor: [180, 142, 173], fontStyle: 'bold' },
        margin: { left: 20, right: 20 }
      });

      // Save the PDF
      doc.save('emergency_response_report.pdf');
    }
  </script>
</div>
