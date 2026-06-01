import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
  selector: 'app-pagination',
  template: `
    <div class="pagination" *ngIf="lastPage > 1">
      <button type="button" class="btn btn-secondary btn-sm" [disabled]="currentPage <= 1" (click)="goTo(currentPage - 1)">Prev</button>
      <span class="text-muted">Page {{ currentPage }} of {{ lastPage }}</span>
      <button type="button" class="btn btn-secondary btn-sm" [disabled]="currentPage >= lastPage" (click)="goTo(currentPage + 1)">Next</button>
    </div>
  `,
  styles: [`
    .pagination {
      display: flex;
      gap: 12px;
      align-items: center;
      justify-content: center;
      margin-top: 24px;
    }
  `],
})
export class PaginationComponent {
  @Input() currentPage = 1;
  @Input() lastPage = 1;
  @Output() pageChange = new EventEmitter<number>();

  goTo(page: number): void {
    if (page >= 1 && page <= this.lastPage) {
      this.pageChange.emit(page);
    }
  }
}
