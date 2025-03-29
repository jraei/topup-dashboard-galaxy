
import { 
  CreditCard, 
  Users, 
  ShoppingCart, 
  DollarSign,
  ArrowUpRight,
  BarChart3,
  TrendingUp
} from "lucide-react";
import StatsCard from "@/components/admin/StatsCard";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { 
  Area, 
  AreaChart, 
  Bar, 
  BarChart, 
  CartesianGrid, 
  Line, 
  LineChart,
  ResponsiveContainer, 
  Tooltip, 
  XAxis, 
  YAxis 
} from "recharts";

// Mock data for charts
const revenueData = [
  { month: "Jan", revenue: 4200 },
  { month: "Feb", revenue: 5800 },
  { month: "Mar", revenue: 7500 },
  { month: "Apr", revenue: 6800 },
  { month: "May", revenue: 9200 },
  { month: "Jun", revenue: 10500 },
  { month: "Jul", revenue: 8700 },
];

const transactionsData = [
  { day: "Mon", transactions: 145 },
  { day: "Tue", transactions: 132 },
  { day: "Wed", transactions: 164 },
  { day: "Thu", transactions: 188 },
  { day: "Fri", transactions: 219 },
  { day: "Sat", transactions: 241 },
  { day: "Sun", transactions: 203 },
];

const productPerformanceData = [
  { name: "Mobile Legends", sales: 420 },
  { name: "Free Fire", sales: 380 },
  { name: "PUBG Mobile", sales: 350 },
  { name: "Genshin Impact", sales: 310 },
  { name: "Call of Duty", sales: 280 },
];

const Dashboard = () => {
  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-slate-100">Dashboard</h1>
        <div className="text-sm text-slate-400">Last updated: Today at 12:45 PM</div>
      </div>
      
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <StatsCard
          title="Total Revenue"
          value="$23,455"
          icon={<DollarSign size={20} />}
          trend={{ value: 12.5, isPositive: true }}
        />
        
        <StatsCard
          title="Total Users"
          value="1,249"
          icon={<Users size={20} />}
          trend={{ value: 8.2, isPositive: true }}
        />
        
        <StatsCard
          title="Total Orders"
          value="842"
          icon={<ShoppingCart size={20} />}
          trend={{ value: 3.7, isPositive: true }}
        />
        
        <StatsCard
          title="Payment Success Rate"
          value="98.5%"
          icon={<CreditCard size={20} />}
          trend={{ value: 1.2, isPositive: true }}
        />
      </div>
      
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <Card className="bg-slate-900/70 border-slate-800">
          <CardHeader className="pb-2">
            <div className="flex items-center justify-between">
              <CardTitle className="text-slate-200 text-lg font-medium">Monthly Revenue</CardTitle>
              <div className="flex items-center text-xs text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded">
                <ArrowUpRight size={12} className="mr-1" /> +22.5% 
              </div>
            </div>
          </CardHeader>
          <CardContent className="pt-2">
            <ResponsiveContainer width="100%" height={300}>
              <AreaChart data={revenueData}>
                <defs>
                  <linearGradient id="revenueGradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#7e22ce" stopOpacity={0.8} />
                    <stop offset="95%" stopColor="#7e22ce" stopOpacity={0.1} />
                  </linearGradient>
                </defs>
                <XAxis 
                  dataKey="month" 
                  stroke="#94a3b8" 
                  fontSize={12} 
                  tickLine={false} 
                  axisLine={false} 
                />
                <YAxis 
                  stroke="#94a3b8" 
                  fontSize={12} 
                  tickLine={false} 
                  axisLine={false} 
                  tickFormatter={(value) => `$${value}`} 
                />
                <CartesianGrid stroke="#334155" strokeDasharray="3 3" vertical={false} />
                <Tooltip 
                  contentStyle={{ 
                    backgroundColor: '#1e293b', 
                    borderColor: '#475569',
                    color: '#f1f5f9',
                    borderRadius: '0.375rem'
                  }} 
                  formatter={(value) => [`$${value}`, 'Revenue']}
                />
                <Area 
                  type="monotone" 
                  dataKey="revenue" 
                  stroke="#7e22ce" 
                  fill="url(#revenueGradient)" 
                  strokeWidth={2} 
                />
              </AreaChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>
        
        <Card className="bg-slate-900/70 border-slate-800">
          <CardHeader className="pb-2">
            <div className="flex items-center justify-between">
              <CardTitle className="text-slate-200 text-lg font-medium">Daily Transactions</CardTitle>
              <BarChart3 size={18} className="text-slate-400" />
            </div>
          </CardHeader>
          <CardContent className="pt-2">
            <ResponsiveContainer width="100%" height={300}>
              <BarChart data={transactionsData}>
                <XAxis 
                  dataKey="day" 
                  stroke="#94a3b8" 
                  fontSize={12} 
                  tickLine={false} 
                  axisLine={false} 
                />
                <YAxis 
                  stroke="#94a3b8" 
                  fontSize={12} 
                  tickLine={false} 
                  axisLine={false} 
                />
                <CartesianGrid stroke="#334155" strokeDasharray="3 3" vertical={false} />
                <Tooltip 
                  contentStyle={{ 
                    backgroundColor: '#1e293b', 
                    borderColor: '#475569',
                    color: '#f1f5f9',
                    borderRadius: '0.375rem'
                  }} 
                />
                <Bar 
                  dataKey="transactions" 
                  fill="#10b981" 
                  radius={[4, 4, 0, 0]} 
                />
              </BarChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>
      </div>
      
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <Card className="bg-slate-900/70 border-slate-800 col-span-1 lg:col-span-2">
          <CardHeader className="pb-2">
            <div className="flex items-center justify-between">
              <CardTitle className="text-slate-200 text-lg font-medium">Recent Transactions</CardTitle>
            </div>
          </CardHeader>
          <CardContent className="pt-2">
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b border-slate-800">
                    <th className="text-left font-medium py-3 text-slate-400">ID</th>
                    <th className="text-left font-medium py-3 text-slate-400">Product</th>
                    <th className="text-left font-medium py-3 text-slate-400">User</th>
                    <th className="text-left font-medium py-3 text-slate-400">Amount</th>
                    <th className="text-left font-medium py-3 text-slate-400">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-800">
                  <tr>
                    <td className="py-3 text-slate-300">#TRX-5829</td>
                    <td className="py-3 text-slate-300">Mobile Legends</td>
                    <td className="py-3 text-slate-300">john_doe</td>
                    <td className="py-3 text-slate-300">$25.00</td>
                    <td className="py-3">
                      <span className="px-2 py-1 text-xs rounded-full bg-green-500/10 text-green-500">
                        Completed
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td className="py-3 text-slate-300">#TRX-5828</td>
                    <td className="py-3 text-slate-300">Free Fire</td>
                    <td className="py-3 text-slate-300">gamer123</td>
                    <td className="py-3 text-slate-300">$10.00</td>
                    <td className="py-3">
                      <span className="px-2 py-1 text-xs rounded-full bg-green-500/10 text-green-500">
                        Completed
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td className="py-3 text-slate-300">#TRX-5827</td>
                    <td className="py-3 text-slate-300">PUBG Mobile</td>
                    <td className="py-3 text-slate-300">sarah_j</td>
                    <td className="py-3 text-slate-300">$15.00</td>
                    <td className="py-3">
                      <span className="px-2 py-1 text-xs rounded-full bg-yellow-500/10 text-yellow-500">
                        Processing
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td className="py-3 text-slate-300">#TRX-5826</td>
                    <td className="py-3 text-slate-300">Steam Wallet</td>
                    <td className="py-3 text-slate-300">alex_gamer</td>
                    <td className="py-3 text-slate-300">$50.00</td>
                    <td className="py-3">
                      <span className="px-2 py-1 text-xs rounded-full bg-green-500/10 text-green-500">
                        Completed
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td className="py-3 text-slate-300">#TRX-5825</td>
                    <td className="py-3 text-slate-300">Call of Duty</td>
                    <td className="py-3 text-slate-300">gaming_pro</td>
                    <td className="py-3 text-slate-300">$20.00</td>
                    <td className="py-3">
                      <span className="px-2 py-1 text-xs rounded-full bg-red-500/10 text-red-500">
                        Failed
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>
        
        <Card className="bg-slate-900/70 border-slate-800">
          <CardHeader className="pb-2">
            <div className="flex items-center justify-between">
              <CardTitle className="text-slate-200 text-lg font-medium">Top Products</CardTitle>
              <TrendingUp size={18} className="text-slate-400" />
            </div>
          </CardHeader>
          <CardContent className="pt-2">
            <ResponsiveContainer width="100%" height={300}>
              <BarChart 
                data={productPerformanceData} 
                layout="vertical"
              >
                <XAxis type="number" stroke="#94a3b8" fontSize={12} tickLine={false} axisLine={false} />
                <YAxis 
                  dataKey="name" 
                  type="category" 
                  stroke="#94a3b8" 
                  fontSize={12} 
                  tickLine={false} 
                  axisLine={false} 
                  width={100}
                />
                <CartesianGrid stroke="#334155" strokeDasharray="3 3" horizontal={false} />
                <Tooltip 
                  contentStyle={{ 
                    backgroundColor: '#1e293b', 
                    borderColor: '#475569',
                    color: '#f1f5f9',
                    borderRadius: '0.375rem'
                  }} 
                />
                <Bar 
                  dataKey="sales" 
                  fill="#7e22ce" 
                  radius={[0, 4, 4, 0]} 
                />
              </BarChart>
            </ResponsiveContainer>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default Dashboard;
