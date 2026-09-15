/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        folium: {
          // Fondo / Pergamino (Calibrados con contraste editorial definido)
          canvas: '#F9F6F0',        // Fondo general de aplicación
          parchment: '#EBE3D3',     // Fondo de tarjeta/sección con contraste claro sobre canvas
          ivory: '#FFFDF9',         // Superficie de papel marfil puro para elementos activos
          border: '#D4C8B0',        // Borde de sección principal visible
          'border-subtle': '#E2D7C3', // Divisores secundarios legibles

          // Tinta / Tipografía
          ink: '#152219',           // Tinta negra-bosque profunda para titulares
          moss: '#2D3930',          // Texto de cuerpo con alto contraste
          sage: '#58685C',          // Etiquetas y metadatos secundarios
          muted: '#849387',         // Texto deshabilitado o secundario

          // Primario (Verde Laurel)
          forest: {
            DEFAULT: '#2D5A3F',
            hover: '#1F402C',
            subtle: '#E1EFE4',
          },

          // Acento: Terracota / Cuero Envejecido
          terracotta: {
            DEFAULT: '#9E4E36',   // Acento cálido principal
            deep: '#6E2D1C',      // Texto de alto contraste
            light: '#C47055',     // Bordes activos / Enfoque
            subtle: '#F5E8E2',    // Fondos de badges con contraste suave
            badge: '#EAD6CC',     // Fondo de badge terracota
          },

          // Estados complementarios
          amber: {
            DEFAULT: '#B87333',   // En tránsito / ILL
            subtle: '#FBF3EB',
          },
          crimson: {
            DEFAULT: '#8C433E',
            subtle: '#F9EDED',
          }
        },
      },
      fontFamily: {
        serif: ['"Newsreader"', '"Cormorant Garamond"', '"Playfair Display"', 'Georgia', 'serif'],
        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
        mono: ['"JetBrains Mono"', 'monospace'],
      },
      boxShadow: {
        'paper-sm': '0 2px 8px -2px rgba(21, 34, 25, 0.08), 0 1px 4px 0 rgba(21, 34, 25, 0.06)',
        'paper-md': '0 6px 20px -2px rgba(21, 34, 25, 0.12), 0 2px 8px -1px rgba(21, 34, 25, 0.08)',
      },
    },
  },
  plugins: [],
};
