class SynthriFooter extends HTMLElement {
  connectedCallback() {
    this.attachShadow({ mode: 'open' });
    this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: block;
          background: #1f2937;
          color: white;
        }
        
        .container {
          max-width: 1280px;
          margin: 0 auto;
          padding: 4rem 2rem;
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
          gap: 3rem;
        }
        
        .logo {
          display: flex;
          align-items: center;
          gap: 0.75rem;
          margin-bottom: 1.5rem;
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
          font-family: 'Space Grotesk', sans-serif;
        }
        
        .footer-description {
          color: #9ca3af;
          line-height: 1.6;
          margin-bottom: 2rem;
        }
        
        .social-links {
          display: flex;
          gap: 1rem;
        }
        
        .social-link {
          width: 40px;
          height: 40px;
          border-radius: 50%;
          background: rgba(255, 255, 255, 0.1);
          display: flex;
          align-items: center;
          justify-content: center;
          transition: background 0.2s;
        }
        
        .social-link:hover {
          background: rgba(255, 255, 255, 0.2);
        }
        
        .footer-title {
          font-size: 1.125rem;
          font-weight: bold;
          margin-bottom: 1.5rem;
          font-family: 'Space Grotesk', sans-serif;
        }
        
        .footer-links {
          list-style: none;
          padding: 0;
          margin: 0;
        }
        
        .footer-links li {
          margin-bottom: 0.75rem;
        }
        
        .footer-links a {
          color: #9ca3af;
          text-decoration: none;
          transition: color 0.2s;
        }
        
        .footer-links a:hover {
          color: white;
        }
        
        .footer-bottom {
          border-top: 1px solid rgba(255, 255, 255, 0.1);
          padding: 2rem;
          text-align: center;
          color: #9ca3af;
          font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
          .container {
            grid-template-columns: 1fr;
          }
        }
      </style>
      
      <div class="container">
        <div>
          <div class="logo">
            <div class="logo-icon">SF</div>
            <span class="logo-text">SynthriForge Edu</span>
          </div>
          <p class="footer-description">
            La plateforme académique nouvelle génération pour connecter et gérer plusieurs établissements scolaires.
          </p>
          <div class="social-links">
            <a href="#" class="social-link">
              <i data-feather="twitter"></i>
            </a>
            <a href="#" class="social-link">
              <i data-feather="linkedin"></i>
            </a>
            <a href="#" class="social-link">
              <i data-feather="facebook"></i>
            </a>
            <a href="#" class="social-link">
              <i data-feather="youtube"></i>
            </a>
          </div>
        </div>
        
        <div>
          <h3 class="footer-title">Produit</h3>
          <ul class="footer-links">
            <li><a href="#">Fonctionnalités</a></li>
            <li><a href="#">Tarifs</a></li>
            <li><a href="#">Documentation</a></li>
            <li><a href="#">Nouveautés</a></li>
          </ul>
        </div>
        
        <div>
          <h3 class="footer-title">Entreprise</h3>
          <ul class="footer-links">
            <li><a href="#">À propos</a></li>
            <li><a href="#">Carrières</a></li>
            <li><a href="#">Presse</a></li>
            <li><a href="#">Contact</a></li>
          </ul>
        </div>
        
        <div>
          <h3 class="footer-title">Ressources</h3>
          <ul class="footer-links">
            <li><a href="#">Blog</a></li>
            <li><a href="#">Centre d'aide</a></li>
            <li><a href="#">Webinaires</a></li>
            <li><a href="#">Statut</a></li>
          </ul>
        </div>
      </div>
      
      <div class="footer-bottom">
        &copy; 2023 SynthriForge Edu. Tous droits réservés.
      </div>
    `;
    
    // Replace feather icons after content is loaded
    this.shadowRoot.querySelectorAll('[data-feather]').forEach(el => {
      feather.replace(el);
    });
  }
}

customElements.define('synthri-footer', SynthriFooter);