
import { useState } from "react";
import { ChevronLeft, ChevronRight, Search, MoreHorizontal, ArrowUpDown } from "lucide-react";
import { 
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

interface Column {
  header: string;
  accessorKey: string;
  cell?: (info: any) => React.ReactNode;
}

interface DataTableProps {
  data: any[];
  columns: Column[];
  onEdit?: (item: any) => void;
  onDelete?: (item: any) => void;
}

const DataTable = ({ data, columns, onEdit, onDelete }: DataTableProps) => {
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [sortBy, setSortBy] = useState<string | null>(null);
  const [sortDirection, setSortDirection] = useState<"asc" | "desc">("asc");
  const itemsPerPage = 10;
  
  // Filter data based on search query
  const filteredData = data.filter(item => {
    return Object.values(item).some(value => 
      value && value.toString().toLowerCase().includes(searchQuery.toLowerCase())
    );
  });
  
  // Sort data
  const sortedData = [...filteredData].sort((a, b) => {
    if (!sortBy) return 0;
    
    if (sortDirection === "asc") {
      return a[sortBy] > b[sortBy] ? 1 : -1;
    } else {
      return a[sortBy] < b[sortBy] ? 1 : -1;
    }
  });
  
  // Paginate data
  const totalPages = Math.ceil(sortedData.length / itemsPerPage);
  const startIndex = (currentPage - 1) * itemsPerPage;
  const paginatedData = sortedData.slice(startIndex, startIndex + itemsPerPage);
  
  const handleSort = (key: string) => {
    if (sortBy === key) {
      setSortDirection(sortDirection === "asc" ? "desc" : "asc");
    } else {
      setSortBy(key);
      setSortDirection("asc");
    }
  };
  
  return (
    <div className="w-full space-y-4">
      <div className="flex flex-col sm:flex-row justify-between gap-4">
        <div className="relative max-w-sm">
          <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-slate-500" />
          <Input
            type="text"
            placeholder="Search..."
            value={searchQuery}
            onChange={(e) => {
              setSearchQuery(e.target.value);
              setCurrentPage(1); // Reset to first page on search
            }}
            className="pl-9 bg-slate-800/50 border-slate-700 text-slate-300"
          />
        </div>
        
        <div className="flex items-center">
          <span className="text-sm text-slate-400">
            Showing {Math.min(startIndex + 1, filteredData.length)} to {Math.min(startIndex + itemsPerPage, filteredData.length)} of {filteredData.length}
          </span>
        </div>
      </div>
      
      <div className="rounded-md border border-slate-800 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead className="bg-slate-800/70">
              <tr>
                {columns.map((column, index) => (
                  <th 
                    key={index}
                    className="text-left whitespace-nowrap px-4 py-3 font-medium text-slate-300"
                  >
                    <div 
                      className="flex items-center cursor-pointer"
                      onClick={() => handleSort(column.accessorKey)}
                    >
                      {column.header}
                      <ArrowUpDown className="ml-1 h-4 w-4" />
                    </div>
                  </th>
                ))}
                {(onEdit || onDelete) && (
                  <th className="w-10 px-4 py-3 text-right">Actions</th>
                )}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800">
              {paginatedData.length > 0 ? (
                paginatedData.map((row, rowIndex) => (
                  <tr 
                    key={rowIndex}
                    className="bg-transparent hover:bg-slate-800/30 transition-colors"
                  >
                    {columns.map((column, colIndex) => (
                      <td 
                        key={colIndex} 
                        className="px-4 py-3 text-slate-300"
                      >
                        {column.cell ? column.cell(row) : row[column.accessorKey]}
                      </td>
                    ))}
                    {(onEdit || onDelete) && (
                      <td className="px-4 py-3 text-right">
                        <DropdownMenu>
                          <DropdownMenuTrigger asChild>
                            <Button 
                              variant="ghost" 
                              className="h-8 w-8 p-0 text-slate-400 hover:text-slate-100"
                            >
                              <MoreHorizontal className="h-4 w-4" />
                            </Button>
                          </DropdownMenuTrigger>
                          <DropdownMenuContent align="end">
                            {onEdit && (
                              <DropdownMenuItem onClick={() => onEdit(row)}>
                                Edit
                              </DropdownMenuItem>
                            )}
                            {onDelete && (
                              <DropdownMenuItem 
                                onClick={() => onDelete(row)}
                                className="text-red-500 focus:text-red-500"
                              >
                                Delete
                              </DropdownMenuItem>
                            )}
                          </DropdownMenuContent>
                        </DropdownMenu>
                      </td>
                    )}
                  </tr>
                ))
              ) : (
                <tr>
                  <td 
                    colSpan={columns.length + (onEdit || onDelete ? 1 : 0)} 
                    className="px-4 py-6 text-center text-slate-500"
                  >
                    No data found
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
      
      {totalPages > 1 && (
        <div className="flex justify-center mt-4">
          <div className="flex items-center space-x-2">
            <Button
              variant="outline"
              size="sm"
              onClick={() => setCurrentPage(p => Math.max(1, p - 1))}
              disabled={currentPage === 1}
              className="bg-slate-800 border-slate-700 hover:bg-slate-700 text-slate-300"
            >
              <ChevronLeft className="h-4 w-4" />
            </Button>
            
            <div className="flex items-center">
              {Array.from({ length: totalPages }, (_, i) => i + 1)
                .filter(page => 
                  page === 1 || 
                  page === totalPages || 
                  (page >= currentPage - 1 && page <= currentPage + 1)
                )
                .map((page, i, pages) => (
                  <>
                    {i > 0 && pages[i - 1] !== page - 1 && (
                      <span key={`ellipsis-${page}`} className="mx-1 text-slate-500">...</span>
                    )}
                    <Button
                      key={page}
                      variant={currentPage === page ? "default" : "outline"}
                      size="sm"
                      onClick={() => setCurrentPage(page)}
                      className={
                        currentPage === page 
                          ? "bg-topup-purple hover:bg-topup-purple/90" 
                          : "bg-slate-800 border-slate-700 hover:bg-slate-700 text-slate-300"
                      }
                    >
                      {page}
                    </Button>
                  </>
                ))}
            </div>
            
            <Button
              variant="outline"
              size="sm"
              onClick={() => setCurrentPage(p => Math.min(totalPages, p + 1))}
              disabled={currentPage === totalPages}
              className="bg-slate-800 border-slate-700 hover:bg-slate-700 text-slate-300"
            >
              <ChevronRight className="h-4 w-4" />
            </Button>
          </div>
        </div>
      )}
    </div>
  );
};

export default DataTable;
