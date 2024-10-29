-- Copyright (C) 2024 Francis Appels <francis.appels@z-application.com>
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.


-- BEGIN MODULEBUILDER INDEXES
ALTER TABLE llx_debweb_debweb ADD INDEX idx_debweb_debweb_rowid (rowid);
ALTER TABLE llx_debweb_debweb ADD INDEX idx_debweb_debweb_ref (ref);
ALTER TABLE llx_debweb_debweb ADD INDEX idx_debweb_debweb_status (status);
-- END MODULEBUILDER INDEXES

--ALTER TABLE llx_debweb_debweb ADD UNIQUE INDEX uk_debweb_debweb_fieldxy(fieldx, fieldy);

--ALTER TABLE llx_debweb_debweb ADD CONSTRAINT llx_debweb_debweb_fk_field FOREIGN KEY (fk_field) REFERENCES llx_debweb_myotherobject(rowid);

