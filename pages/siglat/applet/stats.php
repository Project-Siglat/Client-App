<div class="w-full h-full rounded-lg shadow-lg p-6" style="background-color: #2E3440;">
  <div class="mb-6">
    <label for="date-selector" class="block text-sm font-medium mb-2" style="color: #D8DEE9;">📅 Select Date</label>
    <input type="date" id="date-selector" class="rounded-md px-3 py-2 focus:outline-none focus:ring-2" style="border: 1px solid #4C566A; background-color: #3B4252; color: #ECEFF4; --tw-ring-color: #5E81AC;">
  </div>

  <div class="mb-8 rounded-lg p-6" style="background-color: #3B4252; border: 1px solid #4C566A;">
    <h2 class="text-xl font-semibold mb-4" style="color: #D8DEE9;">📈 Incident Distribution</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Bar Chart -->
      <div>
        <h3 class="text-lg font-medium mb-4" style="color: #D8DEE9;">Incident Counts</h3>
        <div class="space-y-4">
          <div>
            <div class="flex justify-between mb-1">
              <span style="color: #BF616A;">🚗 Vehicular</span>
              <span style="color: #BF616A;">247</span>
            </div>
            <div class="w-full rounded-full h-3" style="background-color: #4C566A;">
              <div class="h-3 rounded-full" style="width: 100%; background-color: #BF616A;"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between mb-1">
              <span style="color: #5E81AC;">⚕️ Medical</span>
              <span style="color: #5E81AC;">183</span>
            </div>
            <div class="w-full rounded-full h-3" style="background-color: #4C566A;">
              <div class="h-3 rounded-full" style="width: 74%; background-color: #5E81AC;"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between mb-1">
              <span style="color: #D08770;">🔥 Fire</span>
              <span style="color: #D08770;">67</span>
            </div>
            <div class="w-full rounded-full h-3" style="background-color: #4C566A;">
              <div class="h-3 rounded-full" style="width: 27%; background-color: #D08770;"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pie Chart Representation -->
      <div>
        <h3 class="text-lg font-medium mb-4" style="color: #D8DEE9;">Distribution Overview</h3>
        <div class="flex items-center justify-center">
          <div class="relative w-40 h-40">
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 42 42">
              <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="#BF616A" stroke-width="3" stroke-dasharray="49.7 100" stroke-dashoffset="0"></circle>
              <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="#5E81AC" stroke-width="3" stroke-dasharray="36.8 100" stroke-dashoffset="-49.7"></circle>
              <circle cx="21" cy="21" r="15.915" fill="transparent" stroke="#D08770" stroke-width="3" stroke-dasharray="13.5 100" stroke-dashoffset="-86.5"></circle>
            </svg>
          </div>
        </div>
        <div class="mt-4 space-y-2">
          <div class="flex items-center">
            <div class="w-3 h-3 rounded-full mr-2" style="background-color: #BF616A;"></div>
            <span class="text-sm" style="color: #D8DEE9;">Vehicular (49.7%)</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 rounded-full mr-2" style="background-color: #5E81AC;"></div>
            <span class="text-sm" style="color: #D8DEE9;">Medical (36.8%)</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 rounded-full mr-2" style="background-color: #D08770;"></div>
            <span class="text-sm" style="color: #D8DEE9;">Fire (13.5%)</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <div class="rounded-lg p-4" style="background-color: #3B4252; border: 1px solid #BF616A;">
      <h3 class="text-lg font-semibold mb-2" style="color: #BF616A;">🚗💥 Vehicular Accident</h3>
      <p class="text-3xl font-bold" style="color: #BF616A;">247</p>
    </div>

    <div class="rounded-lg p-4" style="background-color: #3B4252; border: 1px solid #5E81AC;">
      <h3 class="text-lg font-semibold mb-2" style="color: #5E81AC;">⚕️ Medical Emergency</h3>
      <p class="text-3xl font-bold" style="color: #5E81AC;">183</p>
    </div>

    <div class="rounded-lg p-4" style="background-color: #3B4252; border: 1px solid #A3BE8C;">
      <h3 class="text-lg font-semibold mb-2" style="color: #A3BE8C;">👥 Number of Users</h3>
      <p class="text-3xl font-bold" style="color: #A3BE8C;">1,524</p>
    </div>

    <div class="rounded-lg p-4" style="background-color: #3B4252; border: 1px solid #EBCB8B;">
      <h3 class="text-lg font-semibold mb-2" style="color: #EBCB8B;">🚑 Number of Ambulance</h3>
      <p class="text-3xl font-bold" style="color: #EBCB8B;">12</p>
    </div>

    <div class="rounded-lg p-4" style="background-color: #3B4252; border: 1px solid #B48EAD;">
      <h3 class="text-lg font-semibold mb-2" style="color: #B48EAD;">📊 Total Response</h3>
      <p class="text-3xl font-bold" style="color: #B48EAD;">892</p>
    </div>

    <div class="rounded-lg p-4" style="background-color: #3B4252; border: 1px solid #D08770;">
      <h3 class="text-lg font-semibold mb-2" style="color: #D08770;">🔥 Fire Incident</h3>
      <p class="text-3xl font-bold" style="color: #D08770;">67</p>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const dateSelector = document.getElementById('date-selector');
      const today = new Date();
      const formattedDate = today.getFullYear() + '-' +
        String(today.getMonth() + 1).padStart(2, '0') + '-' +
        String(today.getDate()).padStart(2, '0');
      dateSelector.value = formattedDate;
    });
  </script>
</div>
