# 📊 Monthly Expenses Feature - Complete Guide

## 🎯 **What is the Monthly Expenses Feature?**

The Monthly Expenses feature is an integrated financial tracking system that automatically calculates and displays your business's total monthly expenses across different categories. It provides real-time insights into your spending patterns and helps you monitor cost trends.

## 🔍 **How Monthly Expenses are Calculated**

### **Automatic Calculation Components**

#### 1. **Purchase Costs** 💰
- **Source**: All purchases made during the current month
- **Includes**: Raw materials, inventory, supplies, equipment
- **Calculation**: Sum of all `Purchase.total_amount` for current month
- **Database**: `purchases` table filtered by `purchase_date`

#### 2. **Staff Costs** 👥
- **Source**: All staff daily charges for the current month
- **Includes**: Salaries, wages, overtime, bonuses
- **Calculation**: Sum of all `StaffDailyCharge.total_charge` for current month
- **Database**: `staff_daily_charges` table filtered by `charge_date`

#### 3. **Operating Expenses** 🏢
- **Source**: Manual entries in Profit & Loss statements
- **Includes**: Rent, utilities, insurance, marketing, etc.
- **Calculation**: Manual input in P&L statements
- **Database**: `profit_loss_statements.operating_expenses`

### **Total Monthly Expenses Formula**
```
Monthly Expenses = Purchase Costs + Staff Costs + Operating Expenses
```

## 📍 **Where to Find Monthly Expenses**

### **1. Dashboard Overview** 📈
- **Location**: Main dashboard (`/dashboard`)
- **Display**: Large expense card with current month total
- **Features**: 
  - Current month total expenses
  - Percentage change vs. previous month
  - Color-coded trend indicator (red = increase, green = decrease)

### **2. Profit & Loss Reports** 📊
- **Location**: Profit & Loss section (`/profit-loss`)
- **Display**: Detailed expense breakdown
- **Features**:
  - Expense categories breakdown
  - Period-specific calculations
  - Comparative analysis

### **3. Quick Reports** ⚡
- **Location**: Profit & Loss Quick Report (`/profit-loss-quick`)
- **Display**: Current month expense summary
- **Features**:
  - Real-time calculations
  - Category-wise breakdown
  - Visual charts and graphs

## 🎛️ **How to Use Monthly Expenses**

### **Step 1: Automatic Tracking** 🔄
The system automatically tracks expenses from:

1. **Record Purchases**
   - Navigate to `/purchases`
   - Add new purchases with dates and amounts
   - System automatically includes in monthly calculations

2. **Record Staff Charges**
   - Navigate to `/staff-charges`
   - Add daily staff charges
   - System automatically includes in monthly totals

### **Step 2: Manual Operating Expenses** ✍️
For additional operating expenses:

1. **Create Profit & Loss Statement**
   - Navigate to `/profit-loss`
   - Click "Create New Statement"
   - Enter operating expenses (rent, utilities, etc.)
   - System includes in total calculations

### **Step 3: Monitor and Analyze** 📊

#### **Dashboard Monitoring**
- Check dashboard daily for expense trends
- Monitor month-over-month changes
- Identify spending spikes or reductions

#### **Detailed Analysis**
- Use Profit & Loss reports for detailed breakdowns
- Compare different time periods
- Analyze expense categories

## 📊 **Understanding the Display**

### **Dashboard Card Elements**

#### **Main Amount** 💵
- Large number showing total monthly expenses
- Updates in real-time as new expenses are added
- Formatted in your local currency

#### **Trend Indicator** 📈📉
- **Green Arrow Down**: Expenses decreased (good)
- **Red Arrow Up**: Expenses increased (attention needed)
- **Percentage**: Shows exact change from previous month

#### **Visual Indicators**
- **Red Border**: Indicates this is an expense (cost) metric
- **Receipt Icon**: Visual representation of expenses
- **Color Coding**: Helps quickly identify expense trends

## 🔧 **Managing Monthly Expenses**

### **Expense Categories Tracked**

#### **1. Cost of Goods Sold (COGS)**
- Raw materials purchases
- Inventory purchases
- Direct production costs
- **How to Add**: Record purchases in `/purchases`

#### **2. Staff Costs**
- Employee salaries and wages
- Overtime payments
- Staff-related expenses
- **How to Add**: Record in `/staff-charges`

#### **3. Operating Expenses**
- Rent and utilities
- Insurance premiums
- Marketing and advertising
- Office supplies
- **How to Add**: Enter in Profit & Loss statements

### **Best Practices** ✅

#### **Regular Data Entry**
- Record purchases immediately when made
- Update staff charges daily or weekly
- Enter operating expenses monthly

#### **Expense Monitoring**
- Check dashboard weekly for trends
- Review monthly totals at month-end
- Compare with previous months

#### **Budget Planning**
- Use historical data for budget planning
- Set expense targets based on trends
- Monitor against revenue for profitability

## 📈 **Expense Analysis Features**

### **Trend Analysis**
- Month-over-month comparison
- Percentage change calculations
- Visual trend indicators

### **Category Breakdown**
- Detailed expense categories
- Individual category totals
- Percentage of total expenses

### **Period Comparisons**
- Current vs. previous month
- Year-over-year comparisons
- Custom date range analysis

## 🎯 **Key Benefits**

### **Financial Control** 💪
- Real-time expense tracking
- Immediate visibility into spending
- Early warning for budget overruns

### **Business Intelligence** 🧠
- Spending pattern analysis
- Cost trend identification
- Data-driven decision making

### **Profitability Monitoring** 📊
- Expense vs. revenue tracking
- Profit margin calculations
- Financial health indicators

## 🔄 **Integration with Other Features**

### **Connected Systems**
- **Purchases**: Automatically feeds into expenses
- **Staff Management**: Staff costs included automatically
- **Profit & Loss**: Comprehensive expense reporting
- **Dashboard**: Real-time expense monitoring

### **Data Flow**
```
Purchases → Monthly Expenses ← Staff Charges
     ↓                              ↓
Dashboard Display ← Operating Expenses
     ↓
Profit & Loss Reports
```

## 🚀 **Getting Started**

### **Immediate Steps**
1. **Check Current Status**: Visit dashboard to see current monthly expenses
2. **Review Categories**: Check what's already being tracked
3. **Add Missing Data**: Enter any missing purchases or staff charges
4. **Set Up Regular Entry**: Establish routine for data entry

### **Long-term Setup**
1. **Historical Data**: Enter past months' data for trend analysis
2. **Budget Targets**: Set monthly expense budgets
3. **Regular Reviews**: Schedule monthly expense reviews
4. **Process Improvement**: Refine expense tracking processes

## 📞 **Support and Troubleshooting**

### **Common Issues**
- **Missing Expenses**: Ensure all purchases and staff charges are recorded
- **Incorrect Totals**: Check date ranges and data entry accuracy
- **Trend Anomalies**: Review for one-time expenses or missing data

### **Data Accuracy**
- Verify purchase dates and amounts
- Confirm staff charge calculations
- Double-check operating expense entries

The Monthly Expenses feature provides comprehensive, automated expense tracking that helps you maintain financial control and make informed business decisions!