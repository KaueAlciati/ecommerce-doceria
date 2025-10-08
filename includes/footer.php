    </main>
    <footer class="bg-secondary/50 border-t border-border mt-20">
      <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h3 class="text-2xl font-cookie text-primary mb-4">Doce Encanto</h3>
            <p class="text-muted-foreground">
              Doces artesanais feitos com amor e dedicação para tornar seus momentos ainda mais especiais.
            </p>
          </div>
          <div>
            <h4 class="font-semibold mb-4 text-foreground">Contato</h4>
            <div class="space-y-3 text-muted-foreground">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372a2.25 2.25 0 00-1.743-2.19l-3.482-.696a2.25 2.25 0 00-2.107.65l-.97.97a12.035 12.035 0 01-5.36-5.36l.97-.97a2.25 2.25 0 00.65-2.107l-.696-3.482A2.25 2.25 0 008.872 2.25H7.5A2.25 2.25 0 005.25 4.5v2.25z" />
                </svg>
                <span>(11) 99999-9999</span>
              </div>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.26 0L3.32 8.91A2.25 2.25 0 012.25 6.993V6.75" />
                </svg>
                <span>contato@doceencanto.com.br</span>
              </div>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                </svg>
                <span>São Paulo, SP</span>
              </div>
            </div>
          </div>
          <div>
            <h4 class="font-semibold mb-4 text-foreground">Redes Sociais</h4>
            <div class="flex gap-4">
              <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-primary/10 hover:bg-primary flex items-center justify-center transition-colors group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary group-hover:text-primary-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <rect x="4.5" y="4.5" width="15" height="15" rx="4" ry="4"></rect>
                  <circle cx="12" cy="12" r="3"></circle>
                  <circle cx="16.5" cy="7.5" r="1"></circle>
                </svg>
              </a>
              <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-primary/10 hover:bg-primary flex items-center justify-center transition-colors group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary group-hover:text-primary-foreground" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M22 12.07C22 6.48 17.52 2 11.93 2 6.34 2 1.86 6.48 1.86 12.07c0 4.99 3.64 9.13 8.4 9.93v-7.03H7.9v-2.9h2.36V9.62c0-2.33 1.38-3.62 3.5-3.62.99 0 2.02.18 2.02.18v2.21h-1.14c-1.12 0-1.47.7-1.47 1.42v1.7h2.5l-.4 2.9h-2.1V22c4.76-.8 8.4-4.94 8.4-9.93z" />
                </svg>
              </a>
            </div>
          </div>
        </div>
        <div class="border-t border-border mt-8 pt-8 text-center text-muted-foreground text-sm">
          © 2025 Doce Encanto. Todos os direitos reservados.
        </div>
      </div>
    </footer>
    <script>
      const menuButton = document.getElementById('mobile-menu-button');
      const mobileMenu = document.getElementById('mobile-menu');
      const iconOpen = document.getElementById('mobile-menu-icon-open');
      const iconClose = document.getElementById('mobile-menu-icon-close');

      if (menuButton && mobileMenu) {
        menuButton.addEventListener('click', () => {
          mobileMenu.classList.toggle('hidden');
          iconOpen.classList.toggle('hidden');
          iconClose.classList.toggle('hidden');
        });
      }
    </script>
  </body>
</html>