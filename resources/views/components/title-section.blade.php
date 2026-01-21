 <header class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
     <div>
         <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
             {{ $title }}
         </h2>
         <p class="mt-2 text-lg text-gray-600">
             {{ $description }}
         </p>
     </div>
     <div class="flex items-center gap-3">
         {{ $slot }}
     </div>
 </header>
