import ArtFeed from "./components/ArtFeed";
import Logo from "./components/Logo";

function App() {
  return (
    <>
      <div className="w-full">
        <nav className="w-full flex items-center justify-between gap-4 bg-blue-700 text-white h-[50px] px-4">
          <Logo />
          <div className="gap-1 px-2 inline-flex">
            <button className="border rounded-md p-1">Sign Up</button>
            <button className="border rounded-md p-1 bg-slate-100 text-blue-700">
              Sign In
            </button>
          </div>
        </nav>

        <div className="min-h-[130vh] hero-section"></div>
        <ArtFeed />
        <ArtFeed />
        <ArtFeed />
        <ArtFeed />
        <ArtFeed />
        <ArtFeed />
        <ArtFeed />
      </div>
    </>
  );
}

export default App;
