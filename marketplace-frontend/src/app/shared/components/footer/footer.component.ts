import { Component } from '@angular/core';

@Component({
  selector: 'app-footer',
  template: `
    <footer class="footer">
      <div>© {{ year }} Multi-Vendor Marketplace. Portfolio project by Usama Majeed.</div>
    </footer>
  `,
})
export class FooterComponent {
  year = new Date().getFullYear();
}
