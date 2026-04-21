-- Add performance indexes
-- Run these one at a time if any fail (index may already exist)

ALTER TABLE leases ADD INDEX idx_tenant (tenant_id);
ALTER TABLE leases ADD INDEX idx_unit (unit_id);
ALTER TABLE payments ADD INDEX idx_lease (lease_id);
ALTER TABLE payments ADD INDEX idx_tenant (tenant_id);
ALTER TABLE payments ADD INDEX idx_status (status);
ALTER TABLE units ADD INDEX idx_property (property_id);
ALTER TABLE units ADD INDEX idx_status (status);
ALTER TABLE maintenance_requests ADD INDEX idx_unit (unit_id);
ALTER TABLE maintenance_requests ADD INDEX idx_tenant (tenant_id);
ALTER TABLE notifications ADD INDEX idx_user (user_id);
ALTER TABLE notifications ADD INDEX idx_read (is_read);
ALTER TABLE expenses ADD INDEX idx_property (property_id);
