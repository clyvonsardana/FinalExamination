// Generate sample data
const items = [];
for (let i = 1; i <= 50; i++) {
  items.push(`Item ${i}`);
}

const listElement = document.getElementById("list");
const paginationElement = document.getElementById("pagination");

// Pagination settings
const itemsPerPage = 5;
let currentPage = 1;

// Function to display items
function displayItems(items, wrapper, rowsPerPage, page) {
  wrapper.innerHTML = "";
  page--;

  const start = rowsPerPage * page;
  const end = start + rowsPerPage;
  const paginatedItems = items.slice(start, end);

  for (let i = 0; i < paginatedItems.length; i++) {
    const item = paginatedItems[i];
    const itemElement = document.createElement("li");
    itemElement.textContent = item;
    wrapper.appendChild(itemElement);
  }
}

// Function to create pagination buttons
function setupPagination(items, wrapper, rowsPerPage) {
  wrapper.innerHTML = "";

  const pageCount = Math.ceil(items.length / rowsPerPage);

  // Prev button
  const prevButton = document.createElement("button");
  prevButton.textContent = "Prev";
  prevButton.disabled = currentPage === 1;
  prevButton.addEventListener("click", () => {
    currentPage--;
    displayItems(items, listElement, itemsPerPage, currentPage);
    setupPagination(items, wrapper, rowsPerPage);
  });
  wrapper.appendChild(prevButton);

  // Number buttons
  for (let i = 1; i <= pageCount; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    if (i === currentPage) btn.classList.add("active");

    btn.addEventListener("click", () => {
      currentPage = i;
      displayItems(items, listElement, itemsPerPage, currentPage);
      setupPagination(items, wrapper, rowsPerPage);
    });

    wrapper.appendChild(btn);
  }

  // Next button
  const nextButton = document.createElement("button");
  nextButton.textContent = "Next";
  nextButton.disabled = currentPage === pageCount;
  nextButton.addEventListener("click", () => {
    currentPage++;
    displayItems(items, listElement, itemsPerPage, currentPage);
    setupPagination(items, wrapper, rowsPerPage);
  });
  wrapper.appendChild(nextButton);
}

// Initialize
displayItems(items, listElement, itemsPerPage, currentPage);
setupPagination(items, paginationElement, itemsPerPage);