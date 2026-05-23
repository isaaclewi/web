class SynthriHeader extends HTMLElement {
  connectedCallback() {
    this.attachShadow({ mode: 'open' });
    this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: block;
          position: fixed;
          top: 0;
          left: 0;
          right: 0;
          z-index: 1000;
          background: white;
          box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
          animation: slideDown 0.5s ease-out;
        }
        
        @keyframes slideDown {
          from {
            transform: translateY(-100%);
            opacity: 0;
          }
          to {
            transform: translateY(0);
            opacity: 1;
          }
        }
        
        .container {
          max-width: 1280px;
          margin: 0 auto;
          padding: 1rem 2rem;
          display: flex;
          justify-content: space-between;
          align-items: center;
        }
        
        .logo {
          display: flex;
          align-items: center;
          gap: 0.75rem;
          text-decoration: none;
        }
        
        .logo-icon {
          width: 40px;
          height: 40px;
          background: linear-gradient(135deg, #3b82f6, #8b5cf6);
          border-radius: 10px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-weight: bold;
          font-size: 1.25rem;
          font-family: 'Space Grotesk', sans-serif;
        }
        
        .logo-text {
          font-size: 1.5rem;
          font-weight: bold;
          color: #1f2937;
          font-family: 'Space Grotesk', sans-serif;
        }
        
        .nav {
          display: flex;
          align-items: center;
          gap: 2rem;
        }
        
        .nav-link {
          color: #4b5563;
          text-decoration: none;
          font-weight: 500;
          transition: color 0.2s;
          position: relative;
        }
        
        .nav-link:hover {
          color: #3b82f6;
        }
        
        .nav-link::after {
          content: '';
          position: absolute;
          bottom: -4px;
          left: 0;
          width: 0;
          height: 2px;
          background: #3b82f6;
          transition: width 0.3s;
        }
        
        .nav-link:hover::after {
          width: 100%;
        }
        
        .btn {
          padding: 0.75rem 1.5rem;
          border-radius: 0.5rem;
          font-weight: 500;
          transition: all 0.2s;
        }
        
        .btn-primary {
          background: #3b82f6;
          color: white;
        }
        
        .btn-primary:hover {
          background: #2563eb;
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }
        
        @media (max-width: 768px) {
          .nav {
            display: none;
          }
        }
      </style>
      
      <header class="container">
        <a href="/" class="logo">
          <div class="logo-icon">SF</div>
          <span class="logo-text">SyntriForge Edu</span>
        </a>
        
        <nav class="nav">
          <a href="#features" class="nav-link">Fonctionnalités</a>
          <a href="#schools" class="nav-link">Écoles</a>
          <a href="#testimonials" class="nav-link">Témoignages</a>
          <a href="#pricing" class="nav-link">Tarifs</a>
          <a href="/login" class="btn btn-primary">Connexion</a>
        </nav>
      </header>
    `;
  }
}

customElements.define('synthri-header', SynthriHeader);