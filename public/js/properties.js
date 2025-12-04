// -------------------- properties.js --------------------

// DOM elements
const container = document.getElementById('properties-list');
const form = document.querySelector('form');
const resetBtn = document.querySelector('a.btn-secondary');

let propertiesData = [];

/**
 * Render properties in the frontend
 * Uses prop.image_url from backend; fallback to placeholder if missing
 */
function renderProperties(properties) {
    container.innerHTML = '';

    if (!properties.length) {
        container.innerHTML = `
            <div class="col-12">
                <p class="text-center text-muted fs-5">
                    No properties found matching your criteria.
                </p>
            </div>`;
        return;
    }

    properties.forEach(prop => {
        const col = document.createElement('div');
        col.className = 'col';
        col.innerHTML = `
            <div class="card shadow-sm h-100">
                <img src="${prop.image_url || '/images/properties/placeholder.jpg'}" 
                     class="card-img-top object-fit-cover" 
                     alt="${prop.category || 'Property'}">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">${prop.category || 'N/A'}</h5>
                        <span class="badge bg-secondary">ID: ${prop.id}</span>
                    </div>
                    <p class="card-text mb-1">
                        <span class="text-info fw-bold">Status:</span> ${prop.status || 'N/A'}
                    </p>
                    <p class="card-text mb-1">
                        <span class="text-info fw-bold">Location:</span> ${prop.location || 'N/A'}
                    </p>
                    <p class="card-text mb-1">
                        <span class="text-info fw-bold">Type:</span> ${prop.transaction_type ? prop.transaction_type.charAt(0).toUpperCase() + prop.transaction_type.slice(1) : 'N/A'}
                    </p>
                    <p class="card-text mb-3">
                        <span class="text-success fw-bold">Price:</span> ${Number(prop.price || 0).toLocaleString()} EGP
                    </p>
                    <div class="mt-auto">
                        <a href="/properties/${prop.id}" class="btn btn-primary w-100">
                            <i class="fas fa-info-circle me-1"></i> View Details
                        </a>
                    </div>
                </div>
            </div>`;

        // Fallback in case image file is broken
        const imgEl = col.querySelector('img');
        imgEl.onerror = () => {
            imgEl.src = '/images/properties/placeholder.jpg';
        };

        container.appendChild(col);
    });
}

/**
 * Fetch properties from backend JSON API
 * Backend should return prop.image_url that always exists or fallback
 */
async function fetchProperties(filters = {}) {
    const params = new URLSearchParams(filters);

    try {
        const res = await fetch(`/properties/json?${params.toString()}`);
        if (!res.ok) throw new Error('Failed to fetch properties');
        const data = await res.json();
        propertiesData = data;
        renderProperties(data);
    } catch (err) {
        container.innerHTML = `
            <div class="col-12">
                <p class="text-center text-danger fs-5">${err.message}</p>
            </div>`;
    }
}

/**
 * Gather filters from form and fetch properties
 */
function applyFilters() {
    const filters = {
        search_term: document.getElementById('search_term')?.value || '',
        category: document.getElementById('category')?.value || '',
        location: document.getElementById('location')?.value || '',
        min_price: document.getElementById('min_price')?.value || '',
        max_price: document.getElementById('max_price')?.value || '',
        sort_by: document.getElementById('sort_by')?.value || ''
    };

    fetchProperties(filters);
}

// Event listeners
form?.addEventListener('submit', e => {
    e.preventDefault();
    applyFilters();
});

resetBtn?.addEventListener('click', () => {
    form.reset();
    fetchProperties();
});

// Initial load
fetchProperties();

