<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col selection:bg-emerald-500 selection:text-slate-950">
    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-50 glass-panel border-b border-slate-800/60">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20">
            <span class="text-xl">🍃</span>
          </div>
          <div>
            <h1 class="font-bold text-lg leading-tight tracking-tight text-white flex items-center gap-2">
              Folium <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-mono font-medium">OPAC v1.0</span>
            </h1>
            <p class="text-xs text-slate-400 font-medium">Red Bibliotecaria Multi-Sede</p>
          </div>
        </div>

        <nav class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-300">
          <a href="#" class="text-emerald-400 hover:text-emerald-300 transition-colors">Catálogo Público</a>
          <a href="#" class="hover:text-white transition-colors">Disponibilidad por Sede</a>
          <a href="#" class="hover:text-white transition-colors">Mis Préstamos</a>
          <a href="#" class="hover:text-white transition-colors">Transferencias</a>
        </nav>

        <div class="flex items-center space-x-3">
          <button class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-semibold text-sm transition-all shadow-md shadow-emerald-500/20">
            Iniciar Sesión
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
      <!-- Hero Section & Instant Search -->
      <section class="text-center max-w-3xl mx-auto space-y-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-xs font-semibold text-emerald-400">
          <span>✨ Modelo IFLA LRM / FRBR (WEMI)</span>
        </div>
        <h2 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
          Descubre el conocimiento <br/>
          <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">en toda nuestra red de sedes</span>
        </h2>
        <p class="text-slate-400 text-base sm:text-lg">
          Busca obras unificadas, consulta disponibilidades físicas en tiempo real y solicita transferencias interbibliotecarias sin duplicidad de ediciones.
        </p>

        <!-- Search Bar -->
        <div class="relative max-w-2xl mx-auto pt-2">
          <div class="relative flex items-center">
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Buscar por título, autor, materia o ISBN (ej. El Señor de los Anillos)..."
              class="w-full pl-12 pr-28 py-4 rounded-2xl glass-panel text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all text-sm sm:text-base shadow-2xl"
            />
            <svg class="w-6 h-6 text-slate-400 absolute left-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <button class="absolute right-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold rounded-xl text-xs sm:text-sm transition-all">
              Buscar
            </button>
          </div>
        </div>
      </section>

      <!-- Featured Works Grid -->
      <section class="space-y-6">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold text-white tracking-tight">Obras Destacadas en la Red</h3>
            <p class="text-xs text-slate-400">Agrupadas por Obra (Work) con múltiples Expresiones y Ejemplares por Sede</p>
          </div>
          <a href="#" class="text-xs font-semibold text-emerald-400 hover:underline">Ver todas &rarr;</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="work in mockWorks" :key="work.id" class="glass-card rounded-2xl p-6 flex flex-col justify-between space-y-6 transition-all duration-300">
            <div class="space-y-4">
              <div class="flex items-start justify-between gap-3">
                <span class="px-2.5 py-1 rounded-md bg-slate-800 text-emerald-400 text-xs font-mono font-semibold border border-slate-700">Work ID #{{ work.id }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                  {{ work.availableCount }} ejemplar(es) disponible(s)
                </span>
              </div>
              <div>
                <h4 class="text-lg font-bold text-white hover:text-emerald-400 transition-colors cursor-pointer">{{ work.title }}</h4>
                <p class="text-xs text-slate-400 mt-1">Autor: <span class="text-slate-200 font-medium">{{ work.author }}</span></p>
              </div>
              <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">{{ work.abstract }}</p>
            </div>

            <!-- Branch breakdown -->
            <div class="space-y-3 pt-4 border-t border-slate-800/80">
              <span class="text-xs font-semibold text-slate-300 block">Disponibilidad por Sede:</span>
              <div class="flex flex-wrap gap-2">
                <span v-for="branch in work.branches" :key="branch.name" class="px-2 py-1 rounded-lg bg-slate-900 text-xs text-slate-300 flex items-center gap-1.5 border border-slate-800">
                  <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                  {{ branch.name }}: <strong class="text-white">{{ branch.count }}</strong>
                </span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="border-t border-slate-800/60 glass-panel py-6 mt-12">
      <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-400">
        Folium SIGB &copy; {{ new Date().getFullYear() }} — Sistema Bibliotecario Multi-Sede desacoplado basado en Clean Architecture.
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const searchQuery = ref('');

const mockWorks = ref([
  {
    id: 101,
    title: 'El Señor de los Anillos',
    author: 'J.R.R. Tolkien',
    abstract: 'La epopeya fantástica clásica que sigue la travesía para destruir el Anillo Único.',
    availableCount: 3,
    branches: [
      { name: 'Sede Norte', count: 2 },
      { name: 'Sede Sur', count: 1 }
    ]
  },
  {
    id: 102,
    title: 'Cien Años de Soledad',
    author: 'Gabriel García Márquez',
    abstract: 'Historia de la familia Buendía a lo largo de siete generaciones en el pueblo ficticio de Macondo.',
    availableCount: 5,
    branches: [
      { name: 'Sede Central', count: 3 },
      { name: 'Sede Norte', count: 2 }
    ]
  },
  {
    id: 103,
    title: '1984',
    author: 'George Orwell',
    abstract: 'Novela distópica sobre un régimen totalitario de vigilancia masiva encabezado por el Gran Hermano.',
    availableCount: 2,
    branches: [
      { name: 'Sede Sur', count: 2 }
    ]
  }
]);
</script>
