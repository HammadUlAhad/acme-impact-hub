# ACME Impact Hub - Corporate Social Responsibility Platform

[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-green.svg)](https://vuejs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-blue.svg)](https://www.typescriptlang.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-teal.svg)](https://tailwindcss.com)

## Executive Summary

ACME Impact Hub is a comprehensive Corporate Social Responsibility (CSR) platform designed for large international corporations (20,000+ employees). The platform enables employee engagement in social and environmental initiatives through fundraising campaigns and donation management.

Built with modern enterprise-grade technologies, the platform successfully addresses all core requirements for employee campaign creation, secure donation processing, and comprehensive administrative oversight while maintaining the flexibility needed for last-minute payment provider integration decisions.

## Key Features

### For Employees
- **Campaign Creation**: Create and manage fundraising campaigns for meaningful causes
- **Donation System**: Secure donations with multiple payment methods
- **Real-time Tracking**: Monitor campaign progress and donation impact
- **Search & Discovery**: Find campaigns by category, cause, or keyword
- **Donation History**: Complete transaction tracking and confirmations

### For Administrators
- **Campaign Management**: Approve, feature, and moderate campaigns
- **Analytics Dashboard**: Comprehensive reporting and insights
- **User Management**: Role-based access control and employee directory
- **Financial Oversight**: Transaction monitoring and audit trails

### Enterprise Features
- **Multi-Payment Support**: Credit Card, Bank Transfer, Payroll Deduction
- **Role-Based Security**: Admin, Campaign Manager, Employee access levels
- **Audit Compliance**: Complete transaction logging and reporting
- **Mobile Responsive**: Optimized for desktop and mobile access
- **Scalable Architecture**: Built for enterprise-scale deployment

## Technology Stack

### Backend
- **PHP 8.3** - Latest stable version with modern features
- **Laravel 12** - Latest LTS framework with advanced capabilities
- **SQLite** - Lightweight, serverless database (PostgreSQL/MySQL ready)
- **Composer** - Dependency management
- **Pest** - Modern testing framework
- **PHPStan Level 8** - Maximum static analysis for code quality

### Frontend
- **Vue.js 3** - Latest version with Composition API
- **Inertia.js** - Modern monolithic SPA framework
- **TypeScript** - Type-safe JavaScript development
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Next-generation frontend tooling

### Authentication & Authorization
- **Laravel Fortify** - Frontend agnostic authentication with 2FA support
- **Spatie Laravel Permission** - Role-based access control (RBAC)
- **Laravel Policies** - Fine-grained authorization

## Requirements

- **PHP**: >= 8.3
- **Node.js**: >= 18
- **Composer**: Latest
- **SQLite**: Included with PHP

## Quick Start

### 1. Clone and Setup
```bash
git clone https://github.com/HammadUlAhad/acme-impact-hub.git
cd acme-impact-hub
```

### 2. Backend Installation
```bash
# Install PHP dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed
```

### 3. Frontend Installation
```bash
# Install Node.js dependencies
npm install

# Build frontend assets
npm run build
```

### 4. Start Development
```bash
# Start Laravel server
php artisan serve

# For development with hot reload
npm run dev
```

### 5. Access the Application
- **Application**: http://localhost:8000
- **Admin Login**: admin@acme.com / password
- **Campaign Manager**: campaigns@acme.com / password
- **Employee**: john@acme.com / password

## User Roles & Permissions

### Administrator
- Full system access and configuration
- User management and role assignment
- Global analytics and reporting
- System settings and parameters

### Campaign Manager
- Campaign approval and moderation
- Campaign analytics and insights
- Featured campaign management
- Donation oversight

### Employee
- Campaign creation and management
- Donation participation
- Personal donation history
- Campaign discovery and search

## System Architecture

### Core Features Implementation

#### 1. Employee Campaign Management
- **Create Campaigns**: Authenticated employees can create fundraising campaigns
- **Campaign Categories**: Education, Health, Environment, Community, Emergency Relief, Other
- **Campaign Lifecycle**: Draft → Pending → Active → Completed/Cancelled
- **Search & Filter**: By title, description, category, status
- **Goal Tracking**: Real-time progress monitoring with visual indicators
- **Featured Campaigns**: Administrative highlighting of priority initiatives

#### 2. Donation System
- **Multiple Payment Methods**: Credit Card, Bank Transfer, Payroll Deduction
- **Donation Confirmation**: Real-time confirmation with reference numbers
- **Anonymous Donations**: Privacy-focused donation options
- **Donation History**: Complete transaction tracking per employee
- **Real-time Updates**: Campaign progress updates immediately

#### 3. Administrative Panel
- **Role-Based Access**: Admin, Campaign Manager, Employee roles
- **Campaign Approval**: Workflow for campaign activation
- **Analytics Dashboard**: Campaign performance, donation statistics
- **User Management**: Employee directory and role assignment
- **System Configuration**: Platform settings and parameters

### Database Schema Design

#### Core Tables
```sql
users                   -- Employee directory with RBAC
├── campaigns          -- Fundraising initiatives
├── donations          -- Transaction records
├── roles             -- Permission system
├── permissions       -- Granular access control
└── model_has_roles   -- User-role relationships
```

#### Key Relationships
- **Users → Campaigns** (One-to-Many): Employees can create multiple campaigns
- **Campaigns → Donations** (One-to-Many): Campaigns receive multiple donations
- **Users → Donations** (One-to-Many): Employees can make multiple donations
- **Users → Roles** (Many-to-Many): Flexible role assignment

## Security Architecture

### Authentication
- **Multi-Factor Authentication** support via Laravel Fortify
- **Session-based authentication** for web interface
- **CSRF protection** on all state-changing operations
- **Password policies** enforced at application level

### Authorization
- **Role-Based Access Control (RBAC)** with three primary roles:
  - **Admin**: Full system access, user management, global configuration
  - **Campaign Manager**: Campaign approval, moderation, analytics
  - **Employee**: Campaign creation, donation participation

### Data Protection
- **Input validation** using Laravel Form Requests
- **SQL injection prevention** via Eloquent ORM
- **XSS protection** through Vue.js template escaping
- **File upload security** with type/size validation

## Business Logic

### Campaign Lifecycle
```
Draft → Pending → Active → Completed/Cancelled
        ↑         ↓
    (Review)  (Approval)
```

### Payment Processing
```
Donation → Validation → Processing → Confirmation → Campaign Update
```

### Security Model
```
Authentication (Fortify) → Authorization (Policies) → Action Execution
```

## Enterprise Considerations

### Scalability
- **Database Optimization**: Indexed search fields, optimized queries
- **Caching Strategy**: Laravel cache for frequently accessed data
- **Asset Optimization**: Vite build process with code splitting
- **API-Ready Architecture**: Inertia.js enables easy API extraction

### Compliance & Audit
- **Audit Trails**: Comprehensive logging of all financial transactions
- **Data Retention**: Soft deletes for campaign/donation records
- **Financial Reporting**: Export capabilities for compliance reporting
- **User Activity Tracking**: Complete action logging for security audits

### Deployment & DevOps
- **Environment Configuration**: Laravel env files for multi-environment setup
- **Database Migrations**: Version-controlled schema management
- **Automated Testing**: Pest test suite for continuous integration
- **Build Pipeline**: NPM scripts for frontend asset compilation

## Payment System Architecture

The application implements a **Payment Gateway Abstraction Layer** allowing easy integration with multiple payment providers:

- **Credit Card Processing**: Simulated for demo, ready for Stripe/PayPal integration
- **Bank Transfer**: Enterprise banking integration endpoints
- **Payroll Deduction**: HR system integration capabilities

### Payment Provider Flexibility
The abstraction layer addresses the "last minute payment provider decision" requirement by allowing provider swapping without code changes through a consistent interface.

```php
interface PaymentProcessorInterface {
    public function processPayment(Donation $donation): PaymentResult;
    public function refundPayment(Donation $donation): RefundResult;
    public function getPaymentStatus(string $reference): PaymentStatus;
}
```

## Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Static analysis
./vendor/bin/phpstan analyse

# Frontend linting
npm run lint
```

## Monitoring & Analytics

### Built-in Dashboards
- **Campaign Performance**: Success rates, funding progress
- **Donation Analytics**: Volume, patterns, trends
- **User Engagement**: Activity metrics and participation
- **Financial Reports**: Transaction summaries and audits

### Key Metrics
- Total campaigns created and funded
- Employee participation rates
- Average donation amounts
- Campaign success rates by category

## Production Deployment

### Production Setup
```bash
# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build production assets
npm run build

# Run migrations
php artisan migrate --force
```

### Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=sqlite
MAIL_MAILER=smtp
# Add your production settings
```

### Server Requirements
- **PHP**: >= 8.3 with required extensions
- **Web Server**: Apache/Nginx with PHP-FPM
- **Database**: SQLite (included) or PostgreSQL/MySQL for scale
- **Memory**: Minimum 2GB RAM (4GB+ recommended)
- **Storage**: SSD recommended for database performance

## Performance Optimizations

- **Database Indexing**: Optimized query performance
- **Eager Loading**: Prevention of N+1 queries
- **Asset Bundling**: Minimized JavaScript/CSS
- **Image Optimization**: Responsive breakpoints
- **Caching Layer**: Laravel cache for static data

## Security Features

- **CSRF Protection**: All state-changing operations protected
- **SQL Injection Prevention**: Eloquent ORM with prepared statements
- **XSS Protection**: Vue.js template escaping
- **File Upload Security**: Type and size validation
- **Role-Based Access Control**: Granular permission system
- **Audit Trails**: Comprehensive action logging

## Mobile Support

- **Progressive Web App** capabilities
- **Touch-optimized** interface design
- **Responsive breakpoints** for all screen sizes
- **Offline-capable** for basic functionality

## API Integration

While built as a monolithic application with Inertia.js, the architecture supports easy extraction to separate API:

- **Controller Actions**: Already structured for API responses
- **Resource Classes**: JSON transformation ready
- **Authentication**: API token support prepared
- **Rate Limiting**: Configurable API throttling

## Enterprise Features

### Advanced Search & Analytics
- **Full-text search** across campaigns and descriptions
- **Advanced filtering** by category, status, date ranges
- **Real-time analytics** dashboards for management
- **Export capabilities** for reporting requirements

### Multi-language Support Ready
- **Internationalization structure** in place
- **Vue.js i18n** integration points prepared
- **Database schema** supports localized content

### Integration Capabilities
- **API-first architecture** using Inertia.js
- **Webhook support** for external system notifications
- **Import/Export** functionality for bulk operations
- **Single Sign-On (SSO)** integration points prepared

## Technical Challenges Addressed

### 1. Payment Provider Flexibility
- **Challenge**: Unknown payment provider selection
- **Solution**: Abstracted payment interface allowing quick provider swapping

### 2. Multi-tenant Architecture
- **Challenge**: Supporting multiple corporate divisions
- **Solution**: Role-based segmentation with extensible permission system

### 3. Real-time Updates
- **Challenge**: Live campaign progress updates
- **Solution**: Inertia.js partial reloads with optimistic UI updates

### 4. Mobile Responsiveness
- **Challenge**: 20K+ employees need mobile access
- **Solution**: Mobile-first responsive design with touch-optimized interfaces

## Future Enhancements

### Phase 2 Features
- **Team Fundraising**: Department-based campaign collaboration
- **Corporate Matching**: Employer donation matching programs
- **Impact Reporting**: Detailed outcome tracking and reporting
- **Gamification**: Employee engagement through achievement systems

### Integration Roadmap
- **HR System Integration**: Employee data synchronization
- **Finance System Integration**: Automated expense reporting
- **Communication Tools**: Slack/Teams notifications
- **Business Intelligence**: Advanced analytics and predictive insights

## Requirements Compliance

### Core Functional Requirements - COMPLETE
- **Employee Campaign Creation**: Full CRUD operations with approval workflow
- **Campaign Management**: Search, filter, categorize, and feature campaigns
- **Secure Donation System**: Multiple payment methods with confirmation
- **Administration Panel**: Role-based admin dashboard with analytics
- **Payment Flexibility**: Abstraction layer for easy provider integration

### Technical Requirements - COMPLETE
- **Vue.js (Latest)**: Vue.js 3.5 with Composition API and TypeScript
- **PHP (Latest)**: PHP 8.3 with modern features
- **Laravel (Latest)**: Laravel 12 LTS framework
- **Composer**: Dependency management included
- **Pest Testing**: Modern PHP testing framework configured
- **PHPStan Level 8**: Maximum static analysis implemented
- **Database Options**: SQLite primary, PostgreSQL/MySQL support ready

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For enterprise support and customization:
- Email: support@acmeimpacthub.com
- Issues: [GitHub Issues](https://github.com/HammadUlAhad/acme-impact-hub/issues)

## Conclusion

ACME Impact Hub represents a modern, scalable solution for corporate social responsibility management. The architecture balances immediate functionality requirements with long-term scalability and enterprise integration needs, providing a solid foundation for global CSR initiatives.

The platform successfully addresses all core requirements while maintaining flexibility for future payment provider integration and feature expansion. The technology stack ensures maintainability, security, and performance at enterprise scale.

**Built for Corporate Social Responsibility Excellence**