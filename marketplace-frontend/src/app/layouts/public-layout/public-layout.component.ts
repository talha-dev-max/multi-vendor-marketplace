import { Component } from '@angular/core';

@Component({
  selector: 'app-public-layout',
  template: `
    <div class="public-layout">
      <app-navbar></app-navbar>
      <div style="flex: 1;">
        <router-outlet></router-outlet>
      </div>
      <app-footer></app-footer>
    </div>
  `,
})
export class PublicLayoutComponent {}
