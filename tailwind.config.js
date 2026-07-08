/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],

  theme: {
    extend: {
      // ─── Custom Cream Color Palette ─────────────────────────────────────
      colors: {
        cream: {
          bg:     "#FDF6EC",   // background utama — putih krem hangat
          card:   "#FFFAF2",   // card / surface — krem sangat terang
          border: "#E8D9C5",   // border / divider — krem kecoklatan
          accent: "#C8956C",   // aksen / CTA — oranye krem
          text:   "#3B2F2F",   // teks utama — coklat tua
          muted:  "#8B7355",   // teks sekunder / placeholder — coklat medium
        },
      },

      // ─── Custom Font Family ──────────────────────────────────────────────
      fontFamily: {
        sans: ["Inter", "ui-sans-serif", "system-ui", "-apple-system", "sans-serif"],
        display: ["Poppins", "ui-sans-serif", "system-ui", "sans-serif"],
      },

      // ─── Custom Border Radius ────────────────────────────────────────────
      borderRadius: {
        "2xl": "1rem",
        "3xl": "1.5rem",
        "4xl": "2rem",
      },

      // ─── Custom Box Shadow ───────────────────────────────────────────────
      boxShadow: {
        "cream":     "0 2px 12px 0 rgba(200, 149, 108, 0.12)",
        "cream-md":  "0 4px 24px 0 rgba(200, 149, 108, 0.18)",
        "cream-lg":  "0 8px 40px 0 rgba(59, 47, 47, 0.12)",
        "cream-xl":  "0 16px 64px 0 rgba(59, 47, 47, 0.16)",
      },

      // ─── Custom Animations ───────────────────────────────────────────────
      keyframes: {
        "fade-in": {
          "0%":   { opacity: "0", transform: "translateY(8px)" },
          "100%": { opacity: "1", transform: "translateY(0)" },
        },
        "slide-in-right": {
          "0%":   { opacity: "0", transform: "translateX(16px)" },
          "100%": { opacity: "1", transform: "translateX(0)" },
        },
        "scale-in": {
          "0%":   { opacity: "0", transform: "scale(0.97)" },
          "100%": { opacity: "1", transform: "scale(1)" },
        },
        "shimmer": {
          "0%":   { backgroundPosition: "-200% 0" },
          "100%": { backgroundPosition: "200% 0" },
        },
      },
      animation: {
        "fade-in":         "fade-in 0.3s ease-out both",
        "slide-in-right":  "slide-in-right 0.3s ease-out both",
        "scale-in":        "scale-in 0.2s ease-out both",
        "shimmer":         "shimmer 1.8s linear infinite",
      },

      // ─── Background Gradient ─────────────────────────────────────────────
      backgroundImage: {
        "cream-gradient":       "linear-gradient(135deg, #FDF6EC 0%, #FFFAF2 50%, #FDF6EC 100%)",
        "cream-accent-gradient":"linear-gradient(135deg, #C8956C 0%, #D4A574 100%)",
        "cream-warm-gradient":  "linear-gradient(180deg, #FDF6EC 0%, #F5ECD7 100%)",
      },
    },
  },

  plugins: [
    // @tailwindcss/forms sudah diinstall untuk styling form default yang lebih baik
    require("@tailwindcss/forms"),
  ],
};
