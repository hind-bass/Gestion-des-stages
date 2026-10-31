// Dashboard JavaScript - Taskify Inspired
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    initDashboard();
    
    // Initialize sidebar toggle
    initSidebarToggle();
    
    // Initialize tab navigation
    initTabNavigation();
    
    // Initialize filters
    initFilters();
    
    // Initialize charts and data
    loadDashboardData();
});

function initDashboard() {
    console.log('Dashboard initialized');
    
    // Add loading states
    const loadingElements = document.querySelectorAll('.loading');
    loadingElements.forEach(element => {
        setTimeout(() => {
            element.classList.remove('loading');
        }, 1000);
    });
}

function initSidebarToggle() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
    }
    
    // Restore sidebar state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
    }
}

function initTabNavigation() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Show target content
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}

function initFilters() {
    const filterSelects = document.querySelectorAll('.filter-select');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            updateActiveFilters();
            loadFilteredData();
        });
    });
}

function updateActiveFilters() {
    const filterSelects = document.querySelectorAll('.filter-select');
    const activeFiltersElement = document.querySelector('.active-filters');
    
    if (activeFiltersElement) {
        const activeFilters = Array.from(filterSelects)
            .filter(select => select.value && select.value !== 'all')
            .map(select => `${select.name}: ${select.value}`);
        
        if (activeFilters.length > 0) {
            activeFiltersElement.textContent = `Filtres actifs: ${activeFilters.join(', ')}`;
        } else {
            activeFiltersElement.textContent = '';
        }
    }
}

function loadDashboardData() {
    // Load statistics
    loadStatistics();
    
    // Load mood data
    loadMoodData();
    
    // Load task data
    loadTaskData();
    
    // Load alerts
    loadAlerts();
}

function loadStatistics() {
    // Simulate loading statistics data
    const stats = {
        totalEmployees: 2,
        totalClients: 6,
        activeProjects: 4,
        urgentTasks: 5
    };
    
    updateStatCards(stats);
}

function updateStatCards(stats) {
    const statElements = {
        'total-employees': stats.totalEmployees,
        'total-clients': stats.totalClients,
        'active-projects': stats.activeProjects,
        'urgent-tasks': stats.urgentTasks
    };
    
    Object.keys(statElements).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = statElements[id];
        }
    });
}

function loadMoodData() {
    // Simulate loading mood distribution data
    const moodData = {
        excellent: { count: 2, percentage: 25 },
        good: { count: 1, percentage: 12 },
        neutral: { count: 0, percentage: 0 },
        stressed: { count: 2, percentage: 25 },
        exhausted: { count: 3, percentage: 10 }
    };
    
    updateMoodDistribution(moodData);
}

function updateMoodDistribution(moodData) {
    Object.keys(moodData).forEach(mood => {
        const card = document.querySelector(`.mood-card.${mood}`);
        if (card) {
            const countElement = card.querySelector('.mood-count');
            const percentageElement = card.querySelector('.mood-percentage');
            
            if (countElement) {
                countElement.textContent = moodData[mood].count;
            }
            
            if (percentageElement) {
                percentageElement.textContent = `(${moodData[mood].percentage}%)`;
            }
        }
    });
}

function loadTaskData() {
    // Simulate loading task mood data
    const taskData = [
        { employee: 'f fatima', task: 'TEST1 TEST2', mood: 'exhausted', comment: '' },
        { employee: 'f fatima', task: 'test pa2', mood: 'stressed', comment: '' },
        { employee: 'f fatima', task: 'TEST1 TEST2', mood: 'excellent', comment: 'OP' },
        { employee: 'f fatima', task: 'TEST1 TEST2', mood: 'excellent', comment: '' },
        { employee: 'f fatima', task: 'TEST1 TEST2', mood: 'stressed', comment: '' }
    ];
    
    updateTaskTable(taskData);
}

function updateTaskTable(taskData) {
    const tableBody = document.querySelector('#task-mood-table tbody');
    if (tableBody) {
        tableBody.innerHTML = '';
        
        taskData.forEach(task => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${task.employee}</td>
                <td>${task.task}</td>
                <td><span class="mood-badge ${task.mood}">${getMoodLabel(task.mood)}</span></td>
                <td>${task.comment}</td>
            `;
            tableBody.appendChild(row);
        });
    }
}

function loadAlerts() {
    // Simulate loading alerts data
    const alerts = [
        { type: 'danger', title: 'Attention requise', message: '5 signalements de stress ou épuisement' },
        { type: 'warning', title: 'Humeur neutre', message: '0 employés en humeur neutre' },
        { type: 'success', title: 'Moral positif', message: '3 signalements positifs' }
    ];
    
    updateAlerts(alerts);
}

function updateAlerts(alerts) {
    const alertsContainer = document.querySelector('.alerts-grid');
    if (alertsContainer) {
        alertsContainer.innerHTML = '';
        
        alerts.forEach(alert => {
            const alertCard = document.createElement('div');
            alertCard.className = `alert-card ${alert.type}`;
            alertCard.innerHTML = `
                <div class="alert-icon">
                    <i class="fas fa-${getAlertIcon(alert.type)}"></i>
                </div>
                <div class="alert-content">
                    <h4>${alert.title}</h4>
                    <p>${alert.message}</p>
                </div>
            `;
            alertsContainer.appendChild(alertCard);
        });
    }
}

function getMoodLabel(mood) {
    const labels = {
        'excellent': 'Excellent',
        'good': 'Bon',
        'neutral': 'Neutre',
        'stressed': 'Stresse',
        'exhausted': 'Epuise'
    };
    return labels[mood] || mood;
}

function getAlertIcon(type) {
    const icons = {
        'danger': 'exclamation-triangle',
        'warning': 'exclamation-circle',
        'success': 'check-circle'
    };
    return icons[type] || 'info-circle';
}

function loadFilteredData() {
    // This function would load data based on current filters
    console.log('Loading filtered data...');
    
    // Simulate API call delay
    setTimeout(() => {
        loadDashboardData();
    }, 500);
}

// Utility functions
function formatNumber(num) {
    return num.toLocaleString();
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('fr-FR');
}

// Export functions for global access
window.Dashboard = {
    initDashboard,
    loadDashboardData,
    updateStatCards,
    updateMoodDistribution,
    updateTaskTable,
    updateAlerts
}; 