# Production Management Features

This document outlines the comprehensive production management features that have been added to the inventory management system.

## Overview

The system now includes four major production management components:

1. **Recipe/BOM Management** - Define standard material requirements per product
2. **Production Planning** - Calculate material needs based on production orders
3. **Variance Analysis** - Compare actual vs planned material usage
4. **Material Efficiency Reports** - Track waste percentages and efficiency metrics

## 1. Recipe/BOM Management

### Features
- Create and manage recipes for products
- Define material requirements with quantities and units
- Set waste percentages for accurate planning
- Version control for recipes
- Cost calculation per batch and per unit
- Active/inactive recipe management

### Database Tables
- `recipes` - Main recipe information
- `recipe_items` - Individual material requirements

### Key Functionality
- **Recipe Creation**: Define step-by-step material requirements
- **Cost Calculation**: Automatic cost calculation based on material prices
- **Batch Management**: Define standard batch sizes and yields
- **Version Control**: Track different versions of recipes
- **Duplication**: Copy existing recipes for modifications

### Routes
```php
Route::resource('recipes', RecipeController::class);
Route::get('recipes/{recipe}/calculate-cost', [RecipeController::class, 'calculateCost']);
Route::post('recipes/{recipe}/duplicate', [RecipeController::class, 'duplicate']);
```

## 2. Production Planning

### Features
- Create production plans with multiple products
- Link recipes to production items
- Calculate material requirements automatically
- Track estimated vs actual costs
- Manage production timelines
- Approval workflow (Draft → Approved → In Progress → Completed)

### Database Tables
- `production_plans` - Main production plan information
- `production_plan_items` - Individual products in the plan

### Key Functionality
- **Plan Creation**: Define what to produce and when
- **Material Requirements**: Automatic calculation based on recipes
- **Cost Estimation**: Predict material costs before production
- **Progress Tracking**: Monitor production status
- **Approval Workflow**: Control production execution

### Routes
```php
Route::resource('production-plans', ProductionPlanController::class);
Route::patch('production-plans/{productionPlan}/approve', [ProductionPlanController::class, 'approve']);
Route::patch('production-plans/{productionPlan}/start', [ProductionPlanController::class, 'start']);
Route::patch('production-plans/{productionPlan}/complete', [ProductionPlanController::class, 'complete']);
Route::get('production-plans/{productionPlan}/material-requirements', [ProductionPlanController::class, 'materialRequirements']);
```

## 3. Variance Analysis

### Features
- Compare planned vs actual production quantities
- Analyze cost variances
- Calculate efficiency percentages
- Filter by date range, product, or production plan
- Export capabilities

### Key Metrics
- **Quantity Variance**: Difference between planned and actual quantities
- **Cost Variance**: Difference between estimated and actual costs
- **Efficiency Percentage**: Actual quantity / Planned quantity * 100
- **Budget Performance**: Items over/under budget

### Routes
```php
Route::get('production-reports/variance-analysis', [ProductionReportController::class, 'varianceAnalysis']);
Route::get('production-reports/variance-analysis/export', [ProductionReportController::class, 'exportVarianceAnalysis']);
```

## 4. Material Efficiency Reports

### Features
- Track material usage by type (production, waste, testing, etc.)
- Calculate efficiency and waste percentages
- Identify highest waste materials
- Cost analysis of waste
- Filter by material, product, or date range

### Key Metrics
- **Efficiency Percentage**: Production usage / Total usage * 100
- **Waste Percentage**: Waste usage / Total usage * 100
- **Waste Cost**: Total cost of wasted materials
- **Usage Breakdown**: By type (production, waste, testing, adjustment, other)

### Routes
```php
Route::get('production-reports/material-efficiency', [ProductionReportController::class, 'materialEfficiency']);
Route::get('production-reports/material-efficiency/export', [ProductionReportController::class, 'exportMaterialEfficiency']);
```

## Additional Reports

### Production Summary
- Overall production performance
- Production runs and completion rates
- Material usage by product
- Cost analysis

### Cost Analysis
- Detailed cost breakdown by product
- Material cost analysis
- Cost per unit calculations
- Batch size optimization insights

## Navigation

The new features are accessible through the main navigation:

**Production Section:**
- Recipes/BOM
- Production Plans
- Production Reports

## Models and Relationships

### Recipe Model
```php
// Relationships
belongsTo(Product::class)
hasMany(RecipeItem::class)
belongsTo(User::class, 'created_by')

// Key Methods
getTotalMaterialCost()
getEstimatedCostPerUnit()
calculateMaterialRequirements($quantity)
```

### ProductionPlan Model
```php
// Relationships
hasMany(ProductionPlanItem::class)
belongsTo(User::class, 'created_by')
belongsTo(User::class, 'approved_by')

// Key Methods
calculateMaterialRequirements()
getStatusBadgeClass()
```

### Updated Product Model
```php
// New Relationships
hasMany(Recipe::class)
hasOne(Recipe::class, 'activeRecipe')
hasMany(ProductionPlanItem::class)
```

## Installation and Setup

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Seed Sample Data** (optional)
   ```bash
   php artisan db:seed --class=RecipeSeeder
   ```

3. **Access Features**
   - Navigate to `/recipes` for recipe management
   - Navigate to `/production-plans` for production planning
   - Navigate to `/production-reports` for all reports

## Usage Workflow

1. **Create Recipes**
   - Define material requirements for each product
   - Set waste percentages and batch sizes
   - Activate the recipe

2. **Create Production Plans**
   - Select products and quantities to produce
   - System calculates material requirements
   - Approve and start production

3. **Record Actual Usage**
   - Record actual material consumption
   - System tracks variances automatically

4. **Analyze Performance**
   - Use variance analysis to identify inefficiencies
   - Use efficiency reports to reduce waste
   - Use cost analysis to optimize production

## Benefits

- **Improved Planning**: Accurate material requirement calculations
- **Cost Control**: Better cost estimation and variance tracking
- **Waste Reduction**: Identify and reduce material waste
- **Efficiency Monitoring**: Track production efficiency metrics
- **Data-Driven Decisions**: Comprehensive reporting for better decision making

## Future Enhancements

- Integration with purchase planning
- Automated reorder points based on production plans
- Advanced scheduling and capacity planning
- Integration with quality control systems
- Mobile app for production floor data entry