/**
 * Prayer Wall JavaScript
 * Handles all modal interactions, API calls, and DOM updates
 */

class PrayerWall {
    constructor() {
        this.currentCategory = '';
        this.currentPrayerId = null;
        this.currentPrayerOwner = false;
        this.currentPrayerAdmin = false;
        this.currentUserId = null;
        this.currentUserIsAdmin = false;
        this.editMode = false;
        this.editPrayerId = null;
        this.emotionPickerActive = false;
        
        // Predefined emoticons
        this.emoticons = ['🙏', '❤️', '😢', '😊', '🙌', '✝️'];
        
        this.init();
    }

    init() {
        if (window.prayerWallUser) {
            this.currentUserId = window.prayerWallUser.id;
            this.currentUserIsAdmin = window.prayerWallUser.isAdmin;
        }
        this.cacheDOM();
        this.bindEvents();
        this.loadPrayers();
    }

    cacheDOM() {
        // Buttons
        this.newPrayerBtn = document.getElementById('newPrayerBtn');
        this.submitNewPrayer = document.getElementById('submitNewPrayer');
        this.cancelNewPrayer = document.getElementById('cancelNewPrayer');
        this.closeNewPrayerModal = document.getElementById('closeNewPrayerModal');
        this.closePrayerDetailsModal = document.getElementById('closePrayerDetailsModal');
        this.prayingForYouBtn = document.getElementById('prayingForYouBtn');
        this.seePrayingBtn = document.getElementById('seePrayingBtn');
        
        // Forms
        this.newPrayerForm = document.getElementById('newPrayerForm');
        this.prayerTitle = document.getElementById('prayerTitle');
        this.prayerDescription = document.getElementById('prayerDescription');
        this.prayerCategory = document.getElementById('prayerCategory');
        this.prayerAnonymous = document.getElementById('prayerAnonymous');
        
        // Modals
        this.newPrayerModal = document.getElementById('newPrayerModal');
        this.prayerDetailsModal = document.getElementById('prayerDetailsModal');
        
        // Containers
        this.prayersContainer = document.getElementById('prayersContainer');
        this.searchInput = document.getElementById('searchInput');
        
        // Category tabs
        this.categoryTabs = document.querySelectorAll('.prayer-tab');
        
        // Messages
        this.newPrayerMessage = document.getElementById('newPrayerMessage');
        this.detailsMessage = document.getElementById('detailsMessage');
        
        // Details modal
        this.detailsTitle = document.getElementById('detailsTitle');
        this.detailsRequestTitle = document.getElementById('detailsRequestTitle');
        this.detailsDescription = document.getElementById('detailsDescription');
        this.detailsSubmitter = document.getElementById('detailsSubmitter');
        this.detailsCategory = document.getElementById('detailsCategory');
        this.detailsFooter = document.getElementById('detailsFooter');
        this.prayingCount = document.getElementById('prayingCountNum');
        this.prayingList = document.getElementById('prayingList');
        this.prayingUsersList = document.getElementById('prayingUsersList');
        this.emoticonsDisplay = document.getElementById('emoticonsDisplay');
        this.emoticonPicker = document.getElementById('emoticonPicker');
    }

    bindEvents() {
        // Modal triggers
        this.newPrayerBtn.addEventListener('click', () => this.openNewPrayerModal());
        this.closeNewPrayerModal.addEventListener('click', () => this.closeModal(this.newPrayerModal));
        this.closePrayerDetailsModal.addEventListener('click', () => this.closeModal(this.prayerDetailsModal));
        this.cancelNewPrayer.addEventListener('click', () => this.closeModal(this.newPrayerModal));
        
        // Form submission
        this.newPrayerForm.addEventListener('submit', (e) => this.handleNewPrayerSubmit(e));
        
        // Character counters
        this.prayerTitle.addEventListener('input', () => this.updateCharCount('prayerTitle', 'titleCount', 200));
        this.prayerDescription.addEventListener('input', () => this.updateCharCount('prayerDescription', 'descCount', 1000));
        
        // Category tabs
        this.categoryTabs.forEach(tab => {
            tab.addEventListener('click', () => this.switchCategory(tab));
        });
        
        // Search
        this.searchInput.addEventListener('input', (e) => this.handleSearch(e));
        
        // Close modals on outside click
        window.addEventListener('click', (e) => {
            if (e.target === this.newPrayerModal) this.closeModal(this.newPrayerModal);
            if (e.target === this.prayerDetailsModal) this.closeModal(this.prayerDetailsModal);
        });
        
        // Message close buttons
        document.getElementById('closeNewPrayerMessage')?.addEventListener('click', () => {
            this.newPrayerMessage.classList.add('d-none');
        });
        document.getElementById('closeDetailsMessage')?.addEventListener('click', () => {
            this.detailsMessage.classList.add('d-none');
        });
        
        // Praying for you button
        this.prayingForYouBtn.addEventListener('click', () => this.togglePrayingForYou());
        this.seePrayingBtn.addEventListener('click', () => this.togglePrayingList());
        
        // Emoticon reactions
        this.emoticonPicker.addEventListener('click', (e) => {
            if (e.target.classList.contains('emoticon-btn')) {
                this.addEmoticonReaction(e.target.dataset.emoticon);
            }
        });
    }

    openNewPrayerModal(edit = false, prayer = null) {
        this.editMode = edit;
        this.editPrayerId = edit && prayer ? prayer.id : null;
        this.currentPrayerOwner = edit && prayer ? prayer.is_owner : false;
        this.currentPrayerAdmin = edit && prayer ? prayer.is_admin : false;

        this.newPrayerForm.reset();
        this.newPrayerMessage.classList.add('d-none');
        document.getElementById('titleCount').textContent = '0/200 characters';
        document.getElementById('descCount').textContent = '0/1000 characters';

        if (edit && prayer) {
            this.prayerTitle.value = prayer.title;
            this.prayerDescription.value = prayer.description || '';
            this.prayerCategory.value = prayer.category;
            this.prayerAnonymous.checked = prayer.is_anonymous;
            this.prayerAnonymous.parentElement.style.display = 'block';
            document.querySelector('#newPrayerModal .modal-title').textContent = 'Edit Prayer Request';
            this.submitNewPrayer.innerHTML = '<span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true" id="submitSpinner"></span> Save Changes';
        } else {
            this.prayerAnonymous.parentElement.style.display = 'block';
            document.querySelector('#newPrayerModal .modal-title').textContent = 'Share a Prayer Request';
            this.submitNewPrayer.innerHTML = '<span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true" id="submitSpinner"></span> Share Prayer';
        }

        if (edit && !this.currentPrayerOwner && !this.currentPrayerAdmin) {
            this.prayerAnonymous.parentElement.style.display = 'none';
        }

        this.newPrayerModal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    closeModal(modal) {
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }

    showMessage(container, message, isError = false) {
        const messageEl = container.querySelector('[id$="MessageText"]');
        messageEl.textContent = message;
        container.className = `alert alert-dismissible ${isError ? 'alert-danger' : 'alert-success'}`;
        container.classList.remove('d-none');
    }

    updateCharCount(inputId, countId, maxLen) {
        const input = document.getElementById(inputId);
        const countEl = document.getElementById(countId);
        countEl.textContent = `${input.value.length}/${maxLen} characters`;
    }

    async handleNewPrayerSubmit(e) {
        e.preventDefault();
        
        const submitBtn = this.submitNewPrayer;
        const spinner = document.getElementById('submitSpinner');
        
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        
        const payload = {
            title: this.prayerTitle.value,
            description: this.prayerDescription.value,
            category: this.prayerCategory.value,
            is_anonymous: this.prayerAnonymous.checked
        };

        const endpoint = this.editMode ? 'api/pray_edit.php' : 'api/pray_create.php';
        if (this.editMode) {
            payload.prayer_id = this.editPrayerId;
        }

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });
            
            const data = await response.json();
            
            if (data.success) {
                const successMessage = this.editMode ? 'Prayer updated successfully.' : 'Prayer request shared! 🙏';
                this.showMessage(this.newPrayerMessage, successMessage, false);
                setTimeout(() => {
                    this.closeModal(this.newPrayerModal);
                    this.editMode = false;
                    this.editPrayerId = null;
                    this.loadPrayers();
                }, 1200);
            } else {
                this.showMessage(this.newPrayerMessage, data.message || 'Error saving prayer', true);
            }
        } catch (error) {
            this.showMessage(this.newPrayerMessage, 'Error: ' + error.message, true);
        } finally {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
        }
    }

    switchCategory(tab) {
        // Update active tab
        this.categoryTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        // Load prayers for category
        this.currentCategory = tab.dataset.category;
        this.loadPrayers();
    }

    async loadPrayers() {
        try {
            let url = 'api/pray_list.php';
            if (this.currentCategory) {
                url += '?category=' + encodeURIComponent(this.currentCategory);
            }
            
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success) {
                this.currentUserId = data.current_user_id;
                this.currentUserIsAdmin = data.current_user_is_admin;
                this.renderPrayers(data.prayers);
            }
        } catch (error) {
            console.error('Error loading prayers:', error);
            this.prayersContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger">Error loading prayers</div></div>';
        }
    }

    renderPrayers(prayers) {
        this.prayersContainer.innerHTML = '';
        
        if (!prayers || (Array.isArray(prayers) && prayers.length === 0) || Object.keys(prayers).length === 0) {
            this.prayersContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-inbox"></i> No prayers found. Be the first to share a prayer request!
                    </div>
                </div>
            `;
            return;
        }
        
        // Check if prayers are grouped by category or flat array
        if (Array.isArray(prayers)) {
            // Flat array (single category view)
            prayers.forEach(prayer => {
                this.prayersContainer.appendChild(this.createPrayerCard(prayer));
            });
        } else {
            // Grouped by category
            Object.entries(prayers).forEach(([category, categoryPrayers]) => {
                if (categoryPrayers.length > 0) {
                    // Add category header
                    const categoryHeader = document.createElement('div');
                    categoryHeader.className = 'col-12 mt-5 mb-3';
                    categoryHeader.innerHTML = `
                        <h5 class="category-header">
                            <span class="category-badge category-${category}">${this.getCategoryLabel(category)}</span>
                        </h5>
                    `;
                    this.prayersContainer.appendChild(categoryHeader);
                    
                    // Add prayers for this category
                    categoryPrayers.forEach(prayer => {
                        this.prayersContainer.appendChild(this.createPrayerCard(prayer));
                    });
                }
            });
        }
    }

    normalizePrayer(prayer) {
        prayer.can_manage = prayer.can_manage || prayer.user_id == this.currentUserId || prayer.is_admin || false;
        prayer.is_owner = prayer.is_owner || prayer.user_id == this.currentUserId;
        prayer.is_admin = prayer.is_admin || this.currentUserIsAdmin;
        return prayer;
    }

    createPrayerCard(prayer) {
        prayer = this.normalizePrayer(prayer);
        const card = document.createElement('div');
        card.className = 'col-12 col-sm-6 col-md-4 col-lg-3 mb-4';
        card.innerHTML = `
            <div class="prayer-card h-100">
                <div class="prayer-card-header">
                    <h5 class="prayer-card-title">${this.escapeHtml(prayer.title)}</h5>
                    <span class="badge category-badge category-${prayer.category}">
                        ${this.getCategoryLabel(prayer.category)}
                    </span>
                </div>
                
                <div class="prayer-card-body">
                    <small class="text-muted d-block mb-2">
                        ${prayer.creator_name === 'Anonymous' ? '<i class="fas fa-user-secret"></i> Anonymous' : 'by ' + this.escapeHtml(prayer.creator_name)}
                    </small>
                    <small class="text-muted d-block mb-3">${this.formatDate(prayer.created_at)}</small>
                </div>
                
                <div class="prayer-card-footer">
                    <div class="emoticons-summary">
                        ${prayer.emoticons.length > 0 ? 
                            prayer.emoticons.map(e => `<span class="emoticon-count">${e.emoji} ${e.count}</span>`).join('') 
                            : '<span class="text-muted small">No reactions yet</span>'
                        }
                    </div>
                    
                    <div class="prayer-actions mt-3 flex-wrap gap-2">
                        <button class="btn btn-sm btn-outline-primary see-details" data-prayer-id="${prayer.id}">
                            See details <i class="fas fa-arrow-right"></i>
                        </button>
                        ${prayer.can_manage ? `
                            <button class="btn btn-sm btn-outline-warning edit-prayer" data-prayer-id="${prayer.id}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-prayer" data-prayer-id="${prayer.id}">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        ` : ''}
                        <span class="badge bg-info ms-auto">
                            <i class="fas fa-hands-praying"></i> ${prayer.praying_count}
                        </span>
                    </div>
                </div>
            </div>
        `;
        
        card.querySelector('.see-details').addEventListener('click', () => {
            this.openPrayerDetails(prayer);
        });

        const editBtn = card.querySelector('.edit-prayer');
        if (editBtn) {
            editBtn.addEventListener('click', () => {
                this.openNewPrayerModal(true, prayer);
            });
        }

        const deleteBtn = card.querySelector('.delete-prayer');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                this.confirmDeletePrayer(prayer.id);
            });
        }
        
        return card;
    }

    openPrayerDetails(prayer) {
        this.currentPrayerId = prayer.id;
        
        // Update details modal content
        this.detailsRequestTitle.textContent = prayer.title;
        this.detailsSubmitter.innerHTML = prayer.creator_name === 'Anonymous' 
            ? '<i class="fas fa-user-secret"></i> Anonymous'
            : prayer.creator_name;
        this.detailsCategory.textContent = this.getCategoryLabel(prayer.category);
        this.detailsCategory.className = `badge category-${prayer.category}`;
        this.detailsDescription.innerHTML = prayer.description 
            ? `<p>${this.escapeHtml(prayer.description)}</p>` 
            : '<p class="text-muted"><em>No additional details provided</em></p>';
        
        // Update praying count
        this.prayingCount.textContent = prayer.praying_count;
        this.prayingForYouBtn.classList.toggle('active', prayer.user_has_prayed);
        this.prayingForYouBtn.innerHTML = prayer.user_has_prayed 
            ? '<i class="fas fa-check"></i> You are praying'
            : '<i class="fas fa-hands-praying"></i> Praying for You';
        
        // Update emoticons display
        this.renderEmoticons(prayer.emoticons);
        
        // Load praying users
        this.loadPrayingUsers(prayer.id);
        
        // Setup footer buttons (edit/delete)
        this.setupDetailsFooter(prayer);
        
        // Show modal
        this.prayerDetailsModal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    renderEmoticons(emoticons) {
        this.emoticonsDisplay.innerHTML = '';
        if (emoticons.length > 0) {
            const container = document.createElement('div');
            container.className = 'mb-3';
            container.innerHTML = '<small class="text-muted d-block mb-2">Reactions:</small>';
            const emojiDiv = document.createElement('div');
            emojiDiv.innerHTML = emoticons
                .map(e => `<span class="emoticon-count">${e.emoji} ${e.count}</span>`)
                .join('');
            container.appendChild(emojiDiv);
            this.emoticonsDisplay.appendChild(container);
        }
    }

    async loadPrayingUsers(prayerId) {
        try {
            // We'll fetch the full prayer details to get praying count and user list
            // For now, we'll fetch via the list endpoint and filter
            const response = await fetch('api/pray_list.php');
            const data = await response.json();
            
            // Get all prayers and find this one
            let allPrayers = [];
            if (Array.isArray(data.prayers)) {
                allPrayers = data.prayers;
            } else {
                Object.values(data.prayers).forEach(cats => {
                    allPrayers = allPrayers.concat(cats);
                });
            }
            
            const prayer = allPrayers.find(p => p.id == prayerId);
            if (prayer) {
                this.prayingCount.textContent = prayer.praying_count;
            }
        } catch (error) {
            console.error('Error loading praying users:', error);
        }
    }

    togglePrayingForYou() {
        const action = this.prayingForYouBtn.classList.contains('active') ? 'remove' : 'add';
        
        fetch('api/pray_praying.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                prayer_id: this.currentPrayerId,
                action: action
            })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  this.prayingCount.textContent = data.praying_count;
                  this.prayingForYouBtn.classList.toggle('active');
                  
                  if (action === 'add') {
                      this.prayingForYouBtn.innerHTML = '<i class="fas fa-check"></i> You are praying';
                  } else {
                      this.prayingForYouBtn.innerHTML = '<i class="fas fa-hands-praying"></i> Praying for You';
                  }
                  
                  // Reload prayers to update count in main view
                  this.loadPrayers();
              }
          });
    }

    addEmoticonReaction(emoticon) {
        fetch('api/pray_reactions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                prayer_id: this.currentPrayerId,
                emoticon: emoticon
            })
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  // Reload prayer details to show updated emoticons
                  const categoryUrl = this.currentCategory 
                      ? `api/pray_list.php?category=${this.currentCategory}`
                      : 'api/pray_list.php';
                  
                  fetch(categoryUrl)
                      .then(r => r.json())
                      .then(d => {
                          let prayer = null;
                          if (Array.isArray(d.prayers)) {
                              prayer = d.prayers.find(p => p.id == this.currentPrayerId);
                          } else {
                              for (const prayers of Object.values(d.prayers)) {
                                  prayer = prayers.find(p => p.id == this.currentPrayerId);
                                  if (prayer) break;
                              }
                          }
                          
                          if (prayer) {
                              this.renderEmoticons(prayer.emoticons);
                          }
                      });
              }
          });
    }

    togglePrayingList() {
        this.prayingList.classList.toggle('d-none');
    }

    setupDetailsFooter(prayer) {
        this.detailsFooter.innerHTML = '';

        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'btn btn-secondary';
        closeButton.textContent = 'Close';
        closeButton.addEventListener('click', () => this.closeModal(this.prayerDetailsModal));
        this.detailsFooter.appendChild(closeButton);

        if (prayer.can_manage) {
            const editButton = document.createElement('button');
            editButton.type = 'button';
            editButton.className = 'btn btn-warning';
            editButton.textContent = 'Edit';
            editButton.addEventListener('click', () => {
                this.openNewPrayerModal(true, prayer);
                this.closeModal(this.prayerDetailsModal);
            });
            this.detailsFooter.appendChild(editButton);

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'btn btn-danger';
            deleteButton.textContent = 'Delete';
            deleteButton.addEventListener('click', () => this.confirmDeletePrayer(prayer.id));
            this.detailsFooter.appendChild(deleteButton);
        }
    }

    async confirmDeletePrayer(prayerId) {
        if (!confirm('Delete this prayer request? This cannot be undone.')) {
            return;
        }

        try {
            const response = await fetch('api/pray_delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ prayer_id: prayerId })
            });

            const data = await response.json();
            if (data.success) {
                this.closeModal(this.prayerDetailsModal);
                this.loadPrayers();
            } else {
                this.showMessage(this.detailsMessage, data.message || 'Error deleting prayer', true);
            }
        } catch (error) {
            this.showMessage(this.detailsMessage, 'Error: ' + error.message, true);
        }
    }

    async handleSearch(e) {
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            this.loadPrayers();
            return;
        }
        
        try {
            const response = await fetch(`api/pray_search.php?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success) {
                this.currentUserId = data.current_user_id;
                this.currentUserIsAdmin = data.current_user_is_admin;
                this.renderPrayers(data.prayers);
            }
        } catch (error) {
            console.error('Error searching prayers:', error);
        }
    }

    getCategoryLabel(category) {
        const labels = {
            'lauda': '🎵 Lauda',
            'multumire': '🙏 Mulțumire',
            'cerere': '❤️ Cerere',
            'mijlocire': '🕊️ Mijlocire',
            'marturisire': '✝️ Mărturisire'
        };
        return labels[category] || category;
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) return 'just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        if (diffHours < 24) return `${diffHours}h ago`;
        if (diffDays < 7) return `${diffDays}d ago`;
        
        return date.toLocaleDateString();
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new PrayerWall();
});
